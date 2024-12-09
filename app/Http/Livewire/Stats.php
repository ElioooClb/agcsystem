<?php

namespace App\Http\Livewire;

use App\Services\StatisticsService;
use Livewire\Component;
use DateTime;
use App\Models\Chantier;
use App\Models\Event;
use App\Models\User;
use App\Models\Message;
use App\Models\Time;

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
    public $totalConsumedHours = 0;
    public $totalRevenue = 0;
    public $realHourlyRate = 0;

    // Static stats
    public $totalMaterialAmount;
    public $totalServiceAmount;
    public $totalHoursScheduled;
    public $totalHoursDone;

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
        $this->fetchAndDispatchStatistics();
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
        $chantiers = Chantier::all()->load('loadout');
        $workSitesByState = $chantiers->groupBy(function ($chantier) {
            return $chantier->states->status_group;
        });

        $inProgressWorkSites = $workSitesByState['inProgress'] ?? [];
        list($startedWorkSites, $upcomingWorkSites) = $this->partitionWorkSitesByHoursDone($inProgressWorkSites);
        $toBillWorkSites = $workSitesByState['toBill'] ?? [];
        $archivedWorkSites = $workSitesByState['archived'] ?? [];

        // [SPECGT10] Calcul du total des heures affectées
        $this->totalMaterialAmount = 0;
        $this->totalServiceAmount = 0;
        $this->totalHoursScheduled = 0;
        $this->totalServiceAmount = 0;
        $globalHoursDone = 0;

        foreach ($chantiers as $chantier) {
            if (!collect($archivedWorkSites)->contains($chantier)) {
                //calcul total fournitures
                $this->totalMaterialAmount += round($chantier->materialamount);

                //calcul total main d'oeuvre
                $this->totalServiceAmount += round($chantier->serviceamount);

                //calcul total heures prévues
                $this->totalHoursScheduled += round($chantier->hours);

                //calcul total heures affectées
                $chantiersHours = Time::where('chantier_id', $chantier->id)->get();
                $totalHours = $chantiersHours->sum('hours_day') + $chantiersHours->sum('hours_night') + $chantiersHours->sum('hours_travel');
                $this->totalHoursDone += $totalHours;
                $globalHoursDone += $totalHours;

                $chantier->userHours = $this->calculateUserHours($chantier);
            }else{
                $chantiersHours = Time::where('chantier_id', $chantier->id)->get();
                $totalHours = $chantiersHours->sum('hours_day') + $chantiersHours->sum('hours_night') + $chantiersHours->sum('hours_travel');
                $globalHoursDone += $totalHours;
            }
        }

        // Début [SPECGT10][V2.1] - Calcul des totaux par statut & modification du return
        $startedWorkSitesTotals = $this->calculateTotalsByStatus($startedWorkSites);
        $upcomingWorkSitesTotals = $this->calculateTotalsByStatus($upcomingWorkSites);
        $toBillWorkSitesTotals = $this->calculateTotalsByStatus($toBillWorkSites);
        $globalTotals = $this->calculateTotalsByStatus($chantiers);
        
        // calcul revenu par heure
        $averageToBillHourlyRate = number_format($toBillWorkSitesTotals['totalServiceAmount'] / $toBillWorkSitesTotals['totalHoursScheduled'], 2, '.', '');
        $averageUpcomingHourlyRate = number_format($upcomingWorkSitesTotals['totalServiceAmount'] / $upcomingWorkSitesTotals['totalHoursScheduled'], 2, '.', '');

        return view('livewire.stats', compact(
            'startedWorkSites',
            'upcomingWorkSites',
            'toBillWorkSites',
            'archivedWorkSites',
            'startedWorkSitesTotals',
            'upcomingWorkSitesTotals',
            'toBillWorkSitesTotals',
            'globalTotals',
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
        $this->totalConsumedHours = $this->statisticsService->calculTotalproductiveHours();
        $this->totalRevenue = $this->statisticsService->calculTotalRevenue();
        $this->realHourlyRate = $this->totalConsumedHours > 0 ? round($this->totalRevenue / $this->totalConsumedHours, 0) : 0;
        $this->potentialCA = $this->potentialHours * intval($this->averageHourlyRate);
        $this->periodHours = $this->periodConsumedHours + $this->globalUnproductiveHours;
    }

    private function resetStatistics()
    {
        // Dynamic stats
        $this->worksites = [];
        $this->globalUnproductiveHours = 0;
        $this->periodConsumedHours = 0;
        $this->periodUnproductiveHours = 0;
        $this->potentialHours = 0;
        $this->totalConsumedHours = 0;
        $this->totalRevenue = 0;
        $this->realHourlyRate = 0;
        $this->potentialCA = 0;
        $this->periodHours = 0;
        // Static stats
        $this->totalMaterialAmount = 0;
        $this->totalServiceAmount = 0;
        $this->totalHoursScheduled = 0;
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
    public function calculateTotalsByStatus($workSites): array
    {
        $totalMaterialAmount = 0;
        $totalServiceAmount = 0;
        $totalHoursScheduled = 0;

        foreach ($workSites as $workSite) {
            $totalMaterialAmount += round($workSite->materialamount);
            $totalServiceAmount += round($workSite->serviceamount);
            $totalHoursScheduled += round($workSite->hours);
        }

        return [
            'totalMaterialAmount' => $totalMaterialAmount,
            'totalServiceAmount' => $totalServiceAmount,
            'totalHoursScheduled' => $totalHoursScheduled,
        ];
    }
}
