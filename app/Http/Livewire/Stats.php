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

    // Global stats
    public $totalMaterialAmount = 0;
    public $totalServiceAmount = 0;
    public $totalHoursDone = 0;
    public $startedWorksites, $upcomingWorksites, $toBillWorksites, $archivedWorksites;
    public $startedWorksitesTotals, $upcomingWorksitesTotals, $toBillWorksitesTotals, $globalTotals;
    public $rest, $restPercentage, $restValue, $restKpi;

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

    public function handleGlobalStats()
    {
        $chantiers = Chantier::with(['states', 'times'])->get();

        // On sépare les chantiers ARCHIVED des IN_PROGRESS
        $this->archivedWorksites = $chantiers->filter(function ($chantier) {
            return $chantier->state === 'ST_FACT1C';
        });

        $notArchivedworksites = $chantiers->filter(function ($chantier) {
            return $chantier->state !== 'ST_FACT1C';
        });

        // if ($archivedWorksites->count() + $notArchivedworksites->count() !== $chantiers->count()) dd('errors comptage 00');

        // On a séparer les chantiers non démarrés des chantiers en cours
        $this->upcomingWorksites = $notArchivedworksites->filter(fn($chantier) => $chantier->times->isEmpty());

        $inProgressWorksites = $notArchivedworksites->filter(function ($chantier) {
            return $chantier->times->some(function ($time) {
                return ($time->hours_day ?? 0) > 0 || ($time->hours_night ?? 0) > 0 || ($time->hours_travel ?? 0) > 0;
            });
        });

        // if ($notArchivedworksites->count() !== ($upcomingWorksites->count() + $inProgressWorksites->count())) dd('errors comptage 01');

        // On a séparer les chantiers à facturer des chantiers déjà démarrés
        $this->toBillWorksites = $inProgressWorksites->filter(fn($chantier) => $chantier->states->status_group === 'toBill');
        $this->startedWorksites = $inProgressWorksites->filter(fn($chantier) => $chantier->states->status_group !== 'toBill');

        // if ($toBillWorksites->count() + $startedWorksites->count() !== $inProgressWorksites->count()) dd('errors comptage 02');

        // Calcul des totaux matériels, service et heures par groupement de chantiers
        $this->upcomingWorksitesTotals = $this->calculateTotalsByStatus($this->upcomingWorksites);
        $this->startedWorksitesTotals = $this->calculateTotalsByStatus($this->startedWorksites);
        $this->toBillWorksitesTotals = $this->calculateTotalsByStatus($this->toBillWorksites);
        $notArchivedworksitesTotals = $this->calculateTotalsByStatus($notArchivedworksites);
        $this->globalTotals = $this->calculateTotalsByStatus($chantiers);

        $this->totalMaterialAmount = $notArchivedworksitesTotals['totalMaterialAmount'];
        $this->totalServiceAmount = $notArchivedworksitesTotals['totalServiceAmount'];
        $this->totalHoursDone = $notArchivedworksitesTotals['globalHours'];

        // Calcul du reste des heures restantes (heures planifiées + heures à facturer - heures effectuées)
        $this->rest = $this->startedWorksitesTotals['totalHoursScheduled'] + $this->toBillWorksitesTotals['globalHours'] - $this->totalHoursDone;

        // Calcul du pourcentage des heures restantes (heures restantes / heures effectuées)
        $this->restPercentage = round($this->rest / $this->totalHoursDone, 2);

        // Calcul de la valorisation des heures restant à produire (reste * taux horaire moyen * heures estimées des chantiers démarrés)
        $this->restValue = $this->restPercentage * $this->averageHourlyRate * $this->startedWorksitesTotals['totalHoursScheduled'];

        // Le KPI du reste est le reste valorisé / reste
        $this->restKpi = round($this->restValue / $this->rest);
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
        $this->handleGlobalStats();
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
        $this->startedWorksites = [];
        $this->upcomingWorksites = [];
        $this->toBillWorksites = [];
        $this->archivedWorksites = [];
        $this->startedWorksitesTotals = [];
        $this->upcomingWorksitesTotals = [];
        $this->toBillWorksitesTotals = [];
        $this->globalTotals = [];
        $this->rest = 0;
        $this->restPercentage = 0;
        $this->restValue = 0;
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
            $carry['totalHoursScheduled'] += round($worksite->revised_hours ?? 0);

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
}
