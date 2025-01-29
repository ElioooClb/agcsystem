<?php

namespace App\Http\Livewire;

use App\Services\StatisticsService;
use Livewire\Component;
use DateTime;
use App\Models\Chantier;
use App\Models\User;
use App\Models\Time;
use Illuminate\Support\Facades\DB;

/**
 * Class Stats
 * @package App\Http\Livewire
 */
class Stats extends Component
{
    protected StatisticsService $statisticsService;

    // Dynamic stats
    public string $start = '';
    public string $end = '';
    public DateTime $startObject;
    public DateTime $endObject;
    public $worksites = [];
    public $globalUnproductiveHours = 0;
    public $periodConsumedHours = 0;
    public $periodUnproductiveHours = 0;
    public $periodHours = 0;
    public string $averageHourlyRate = '99';
    public $potentialHours = 0;
    public int $potentialCA = 0;
    public $totalRevenue = 0;
    public $realHourlyRate = 0;
    public $periodMaterialAmount = 0;
    public $UnbillableHours = 0;
    public $isArchivedCalculated = false;

    // Static stats
    public $totalMaterialAmount = 0;
    public $totalServiceAmount = 0;
    public $totalHoursDone = 0;

    protected $listeners = ['updateHoursEstimation' => 'handleHoursEstimation', 'updateServiceAmount' => 'handleServiceAmount', 'updateAvgHourlyRate' => 'handleAverageHourlyRate'];

    public function mount(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
        $this->initializeDates();
        $this->fetchAndDispatchStatistics();
    }

    public function hydrate()
    {
        $this->statisticsService = $this->getStatisticsService();
        $this->fetchAndDispatchStatistics();
    }

    public function handleGenerateStats()
    {
        $this->hydrate();
    }

    public function updatedStart($value)
    {
        $this->startObject = new DateTime($value);
        $this->fetchAndDispatchStatistics();
    }

    public function updatedEnd($value)
    {
        $this->endObject = new DateTime($value);
        $this->fetchAndDispatchStatistics();
    }

    public function handleHoursEstimation($data)
    {
        $this->updateWorksite($data, 'revised_hours', 'hoursEstimationUpdated', 'Erreur lors de la mise à jour des heures estimées');
    }

    public function handleServiceAmount($data)
    {
        $this->updateWorksite($data, 'serviceamount', 'serviceAmountUpdated', 'Erreur lors de la mise à jour du montant estimé');
    }

    public function handleAverageHourlyRate($data)
    {
        $this->averageHourlyRate = $data['newValue'];
        $this->fetchAndDispatchStatistics();
        $this->emit('avgHourlyRateUpdated', 'Taux horaire moyen mis à jour');
    }

    public function render()
    {
        $chantiers = Chantier::with(['states', 'times'])->get();

        // Défini des listes de chantiers par statut
        $workSitesByState = $chantiers->groupBy(function ($chantier) {
            return $chantier->states->status_group;
        });

        $toBillTest = Time::whereIn('chantier_id', $chantiers->filter(function ($chantier) {
            return $chantier->states->status_group === 'toBill';
        })->pluck('id'))
            ->sum(DB::raw('hours_day + hours_night + hours_travel'));

        $inProgressWorkSites = $workSitesByState['inProgress'] ?? [];
        list($startedWorkSites, $upcomingWorkSites) = $this->partitionWorkSitesByHoursDone($inProgressWorkSites);
        $toBillWorkSites = $workSitesByState['toBill'] ?? [];
        $archivedWorkSites = $workSitesByState['archived'] ?? [];

        // $archivedIds = collect($archivedWorkSites)->pluck('id'); // Liste des IDs archivés
        // $globalHoursDone = 0;

        // foreach ($chantiers as $chantier) {
        //     // Calcul des heures affectées
        //     $totalHours = $chantier->times->sum(fn($time) => $time->hours_day + $time->hours_night + $time->hours_travel);
        //     $globalHoursDone += $totalHours;

        //     // Si le chantier est archivé, on passe à l'itération suivante
        //     if ($archivedIds->contains($chantier->id)) {
        //         continue;
        //     }

        //     // Ajout des valeurs si non archivé
        //     $this->totalMaterialAmount += round($chantier->materialamount);
        //     $this->totalServiceAmount += round($chantier->serviceamount);
        //     $this->totalHoursDone += $totalHours;

        //     // Calcul des heures utilisateur
        //     $chantier->userHours = $this->calculateUserHours($chantier);
        // }


        // Début [SPECGT10][V2.1] - Calcul des totaux par statut & modification du return
        $startedWorkSitesTotals = $this->calculateTotalsByStatus($startedWorkSites);
        $upcomingWorkSitesTotals = $this->calculateTotalsByStatus($upcomingWorkSites);
        $toBillWorkSitesTotals = $this->calculateTotalsByStatus($toBillWorkSites);
        $globalTotals = $this->calculateTotalsByStatus($chantiers);

        $this->totalMaterialAmount += $toBillWorkSitesTotals['totalMaterialAmount'] + $upcomingWorkSitesTotals['totalMaterialAmount'] + $startedWorkSitesTotals['totalMaterialAmount'];
        $this->totalServiceAmount += $toBillWorkSitesTotals['totalServiceAmount'] + $upcomingWorkSitesTotals['totalServiceAmount'] + $startedWorkSitesTotals['totalServiceAmount'];
        $this->totalHoursDone += $toBillWorkSitesTotals['globalHours'] + $upcomingWorkSitesTotals['globalHours'] + $startedWorkSitesTotals['globalHours'];

        // Calcul du reste des heures restantes (heures planifiées + heures à facturer - heures effectuées)
        $rest = $startedWorkSitesTotals['totalHoursScheduled'] + $toBillWorkSitesTotals['globalHours'] - $this->totalHoursDone;

        // Calcul du pourcentage des heures restantes (heures restantes / heures planifiées * 100)
        $restPercentage = $startedWorkSitesTotals['totalHoursScheduled'] > 0
            ? round($rest / $startedWorkSitesTotals['totalHoursScheduled'], 2)
            : 0;

        // Calcul de la valorisation des heures restant à produire
        $restValue = $restPercentage * $this->averageHourlyRate * $this->totalHoursDone;

        // calcul revenu par heure
        $averageToBillHourlyRate = number_format($toBillWorkSitesTotals['totalServiceAmount'] / $toBillWorkSitesTotals['totalHoursScheduled'], 2, '.', '');
        $averageUpcomingHourlyRate = number_format($upcomingWorkSitesTotals['totalServiceAmount'] / $upcomingWorkSitesTotals['totalHoursScheduled'], 2, '.', '');

        // calcul du reste des heures
        $restUpcoming = $this->calculateRestByStatus($upcomingWorkSites);

        return view('livewire.stats', compact(
            'startedWorkSites',
            'upcomingWorkSites',
            'toBillWorkSites',
            'archivedWorkSites',
            'startedWorkSitesTotals',
            'upcomingWorkSitesTotals',
            'toBillWorkSitesTotals',
            'globalTotals',
            'toBillTest',
            'rest',
            'restPercentage',
            'restValue',
        ));
    }

    private function initializeDates()
    {
        $now = new DateTime();
        $this->startObject = (clone $now)->modify('last Monday');
        $this->endObject = (clone $this->startObject)->modify('+6 days');
        $this->start = $this->startObject->format('Y-m-d');
        $this->end = $this->endObject->format('Y-m-d');
    }

    private function fetchAndDispatchStatistics()
    {
        $this->resetStatistics();
        $this->statisticsService->initData($this->startObject, $this->endObject);
        $this->worksites = $this->statisticsService->getWorksites();
        $this->globalUnproductiveHours = $this->statisticsService->calculAllUsersPeriodUnproductiveHours();
        $this->periodConsumedHours = max(1, $this->statisticsService->calculPeriodProductiveHours());
        $this->periodUnproductiveHours = $this->statisticsService->calculPeriodUnproductiveHours();
        $this->potentialHours = $this->statisticsService->calculPotentialHours();
        $this->totalRevenue = $this->statisticsService->calculTotalRevenue();
        $totalConsumedHours =  $this->statisticsService->calculTotalproductiveHours();
        $this->realHourlyRate = $totalConsumedHours > 0 ? round($this->totalRevenue / $totalConsumedHours, 0) : 0;
        $this->potentialCA = $this->potentialHours * intval($this->averageHourlyRate);
        $this->periodHours = $this->periodConsumedHours + $this->globalUnproductiveHours;
        $this->periodMaterialAmount = $this->statisticsService->calculTotalMaterialAmount();
        $this->UnbillableHours = $this->statisticsService->calculUnbillableHours();
    }

    private function resetStatistics()
    {
        // Dynamic stats
        $this->worksites = [];
        $this->globalUnproductiveHours = 0;
        $this->periodConsumedHours = 0;
        $this->periodUnproductiveHours = 0;
        $this->periodHours = 0;
        $this->potentialHours = 0;
        $this->potentialCA = 0;
        $this->totalRevenue = 0;
        $this->realHourlyRate = 0;
        $this->periodMaterialAmount = 0;
        $this->UnbillableHours = 0;

        // Static stats
        $this->totalMaterialAmount = 0;
        $this->totalServiceAmount = 0;
        $this->totalHoursDone = 0;
    }

    private function updateWorksite($data, $field, $successEvent, $errorMessage)
    {
        try {
            $worksiteID = intval($data['id']);
            $newValue = floatval($data['newValue']);
            $worksite = Chantier::findOrFail($worksiteID);
            $worksite->update([$field => $newValue]);
            $this->fetchAndDispatchStatistics();
            $this->emit($successEvent, ucfirst(str_replace('_', ' ', $field)) . ' mis à jour');
        } catch (\Exception $e) {
            $this->emit('error', $errorMessage);
        }
    }

    private function getStatisticsService()
    {
        if (!isset($this->statisticsService)) {
            $this->statisticsService = app(StatisticsService::class);
        }
        return $this->statisticsService;
    }

    private function partitionWorkSitesByHoursDone($workSites)
    {
        // Convertir le tableau en collection
        $workSites = collect($workSites);

        return $workSites->partition(function ($chantier) {
            // Fetch all time entries for the current work site
            $workSiteHours = Time::where('chantier_id', $chantier->id)->get();
            // Calculate the total hours (day + night) for the current work site
            $totalHours = $workSiteHours->sum('hours_day') + $workSiteHours->sum('hours_night') + $workSiteHours->sum('hours_travel');
            // Return true if total hours is greater than 0, false otherwise
            return $totalHours > 0;
        });
    }

    /**
     * Calculate the total hours for each user at a work site
     * [SPECGT10] Calcul des heures totales pour chaque utilisateur sur un chantier
     * @param $workSite
     * @return array
     */
    public function calculateUserHours($workSite)
    {
        // Get the list of user IDs associated with the work site
        $userByWorkSite = $workSite->users()->pluck('user_id');
        // Fetch all users that are in the list of user IDs
        $listUsers = User::whereIn('id', $userByWorkSite)->get();
        // Initialize an array to store the total hours for each user
        $userHours = [];

        // Loop through each user
        foreach ($listUsers as $user) {
            // Fetch all time entries for the current user at the work site
            $userTimes = Time::where('chantier_id', $workSite->id)
                ->where('user_id', $user->id)
                ->get();
            // Calculate the total hours (day + night) for the current user
            $userTotalHours = $userTimes->sum('hours_day') + $userTimes->sum('hours_night');
            // Store the total hours for the current user in the array
            $userHours[$user->id] = $userTotalHours;
        }

        // Return the array with the total hours for each user
        return $userHours;
    }

    /**
     * Calculate totals by status
     * [SPECGT10] Calcul des totaux par statut
     * @param $workSites
     * @return array
     */
    public function calculateTotalsByStatus($worksites): array
    {
        return $worksites->reduce(function ($carry, $worksite) {
            // Calcul des totaux pour materialamount, serviceamount et hours
            $carry['totalMaterialAmount'] += round($worksite->materialamount ?? 0);
            $carry['totalServiceAmount'] += round($worksite->serviceamount ?? 0);
            $carry['totalHoursScheduled'] += round($worksite->hours ?? 0);

            // Calcul des globalHours via la relation 'times'
            $globalHours = $worksite->times->sum(fn($time) => $time->hours_day + $time->hours_night + $time->hours_travel);
            $carry['globalHours'] += $globalHours;

            return $carry;
        }, [
            'totalMaterialAmount' => 0,
            'totalServiceAmount' => 0,
            'totalHoursScheduled' => 0,
            'globalHours' => 0,
        ]);
    }

    /**
     * Calculate the rest of hours by status
     * [SPECGT10] Calcul du reste des heures par statut
     * @param $worksites
     * @return int
     */
    public function calculateRestByStatus($worksites): int
    {
        $rest = 0;

        foreach ($worksites as $worksite) {
            $consummedHours = $worksite->times->sum('hours_day') + $worksite->times->sum('hours_night') + $worksite->times->sum('hours_travel');
            $rest += $worksite->revised_hours - $consummedHours;
        }

        return $rest;
    }
}
