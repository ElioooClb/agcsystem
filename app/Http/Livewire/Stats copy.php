<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use App\Services\StatisticsService;
use Livewire\Component;
use DateTime;
use App\Models\Chantier;

/**
 * Class Stats extends component
 * @package App\Http\Livewire
 * @version 1.0 [SPECMBA06]
 */
class Stats extends Component
{
    protected StatisticsService $statisticsService;
    public string $start = '';
    public string $end = '';
    public DateTime $startObject;
    public DateTime $endObject;
    public $worksites;
    // Global
    public $globalUnproductiveHours; // Sum of the unproductive hours on the worksites for all users
    // Period
    public $periodConsumedHours; // Sum of the hours worked on the worksites between the two dates
    public $periodUnproductiveHours; // Sum of the unproductive hours on the worksites between the two dates
    public $periodHours; // Sum of the estimated hours of the worksites between the two dates
    // Potential
    public string $averageHourlyRate = '99';
    public $potentialHours; // Sum of the estimated hours of the worksites
    public int $potentialCA; // Calculated potential revenue
    // Consumed
    public $totalConsumedHours; // Sum of the hours worked on the worksites
    public $totalRevenue; // Sum of the estimated amount of the worksites
    public $realHourlyRate; // Calculated real hourly rate
    protected $listeners = ['updateHoursEstimation' => 'handleHoursEstimation', 'updateServiceAmount' => 'handleServiceAmount'];

    /**
     * Update the start date
     * @param $value
     * @version 1.0 [SPECMBA06]
     */
    public function updatedStart($value)
    {
        $this->startObject = new DateTime($value);
        $this->resetData();
        $this->statisticsService->initData($this->startObject, $this->endObject);
        $this->dispatchData();
    }

    /**
     * Update the end date
     * @param $value
     * @version 1.0 [SPECMBA06]
     */
    public function updatedEnd($value)
    {
        $this->endObject = new DateTime($value);
        $this->resetData();
        $this->statisticsService->initData($this->startObject, $this->endObject);
        $this->dispatchData();
    }

    public function mount(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
        $this->worksites = [];
        $this->periodConsumedHours = 1;
        $this->periodUnproductiveHours = 0;
        $this->periodHours = 0;
        $this->potentialHours = 0;
        $this->totalConsumedHours = 0;
        $this->totalRevenue = 0;
        $this->realHourlyRate = 0;

        // Créer un objet DateTime pour la date actuelle
        $now = new DateTime();

        // Obtenir la date du début de la semaine précédente
        $startOfLastWeek = clone $now;
        $startOfLastWeek->modify('last Sunday')->modify('-1 week'); // Début de la semaine précédente (dimanche précédent)
        // $startOfLastWeek = new DateTime('2024-10-01');
        $this->startObject = $startOfLastWeek;
        $this->start = $startOfLastWeek->format('Y-m-d');

        // Obtenir la date de la fin de la semaine précédente
        $endOfLastWeek = clone $startOfLastWeek;
        $endOfLastWeek->modify('next Saturday'); // Fin de la semaine précédente (samedi suivant)
        // $endOfLastWeek = new DateTime('2024-10-31');
        $this->endObject = $endOfLastWeek;
        

        $this->statisticsService->initData($startOfLastWeek, $endOfLastWeek);
        $this->dispatchData();
    }

    public function hydrate()
    {
        $this->resetData();
        $this->statisticsService = $this->getStatisticsService();
        $this->statisticsService->initData($this->startObject, $this->endObject);
        $this->dispatchData();
    }

    public function handleHoursEstimation($data)
    {
        try {
            $worksiteID = intval($data['id']);
            $newValue = floatval($data['newValue']);
            $worksite = Chantier::find($worksiteID);
            $worksite->update(['revised_hours' => $newValue]);
            $worksite->refresh();
            $this->hydrate();
            $this->emit('hoursEstimationUpdated', 'Heures estimées mises à jour');
        } catch (\Exception $e) {
            $this->emit('error', 'Erreur lors de la mise à jour des heures estimées');
        }
    }

    public function handleServiceAmount($data)
    {
        try {
            $worksiteID = intval($data['id']);
            $newValue = floatval($data['newValue']);
            $worksite = Chantier::find($worksiteID);
            $worksite->update(['serviceamount' => $newValue]);
            $worksite->refresh();
            $this->hydrate();
            $this->emit('serviceAmountUpdated', 'Montant estimé mis à jour');
        } catch (\Exception $e) {
            $this->emit('error', 'Erreur lors de la mise à jour du montant estimé');
        }
    }

    /**
     * Handling the statistics of the worksites between two dates
     * @return array
     * @version 1.0 [SPECMBA06]
     */
    public function handleGenerateStats()
    {
        $this->resetData();
        $this->statisticsService = $this->getStatisticsService();
        $this->statisticsService->initData($this->startObject, $this->endObject);
        $this->dispatchData();
    }

    /**
     * Edit the average hourly rate
     * @return array
     * @version 1.0 [SPECMBA06]
     */
    public function handleAverageHourlyRate($data)
    {
        $th = $data['averageTH'];
        if ($th < 0) {
            $th = 0;
        }
        $this->averageHourlyRate = $th;
    }

    public function render()
    {
        return view('livewire.stats');
    }

    private function resetData()
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

    private function dispatchData()
    {
        $this->worksites = $this->statisticsService->getWorksites();
        $this->globalUnproductiveHours = $this->statisticsService->calculAllUsersPeriodUnproductiveHours();
        $this->periodConsumedHours = $this->statisticsService->calculPeriodProductiveHours();
        if ($this->periodConsumedHours <= 0) {
            $this->periodConsumedHours = 1;
        }
        $this->periodUnproductiveHours = $this->statisticsService->calculPeriodUnproductiveHours();
        $this->potentialHours = $this->statisticsService->calculPotentialHours();
        $this->totalConsumedHours = $this->statisticsService->calculTotalproductiveHours();
        $this->totalRevenue = $this->statisticsService->calculTotalRevenue();
        if ($this->totalConsumedHours > 0) {
            $this->realHourlyRate =  round($this->totalRevenue / $this->totalConsumedHours, 0);
        }
        $this->potentialCA = $this->potentialHours * intval($this->averageHourlyRate);
        $this->periodHours = $this->periodConsumedHours + $this->globalUnproductiveHours;
    }

    private function getStatisticsService()
    {
        if (!isset($this->statisticsService)) {
            $this->statisticsService = app(StatisticsService::class);
        }
        return $this->statisticsService;
    }
}
