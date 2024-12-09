<?php

namespace App\Http\Livewire;

use App\Services\StatisticsService;
use Livewire\Component;
use DateTime;
use App\Models\Chantier;

/**
 * Class Stats
 * @package App\Http\Livewire
 */
class Stats extends Component
{
    protected StatisticsService $statisticsService;

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

    protected $listeners = ['updateHoursEstimation' => 'handleHoursEstimation', 'updateServiceAmount' => 'handleServiceAmount'];

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
        $this->averageHourlyRate = max(0, floatval($data['averageTH']));
    }

    public function render()
    {
        return view('livewire.stats');
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
}
