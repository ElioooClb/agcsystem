<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\View\View;
use App\Services\CalculOnHoursService;
use App\Models\User;
use App\Models\Time;
use DateTime;

use function PHPUnit\Framework\isEmpty;

// DEBUT [SPECGT20] - Mise à jour de la fonctionnalité de suivi des heures
class UserScheduleManager extends Component
{
    public string $hasError = '';
    public User $user;
    protected CalculOnHoursService $calculService;
    public array $events;
    protected $listeners = ['onAvailability' => 'handleAvailability', 'onDeleteEvent' => 'handleDeleteEvent', 'onCalculWeekListHours' => 'handleCalculWeekListHours', 'onCalculMonthlyHours' => 'handleCalculMonthlyHours'];

    /**
     * Mount the component and set the user defined in the props.
     * [SPECGT20]
     * @param int $id
     * @return void
     */
    public function mount(int $id, CalculOnHoursService $calculService): void
    {
        $this->user = User::with('times.chantier')->find($id);
        $this->calculService = $calculService;
        $this->calculService->setUser($this->user);
        $this->getScheduleEvents();
    }

    /**
     * Hydrate the component with the CalculOnHoursService.
     * [SPECGT26]
     * @return void
     */
    public function hydrate(): void
    {
        $this->calculService = app(CalculOnHoursService::class);
        $this->calculService->setUser($this->user);
    }

    /**
     * Get the schedule events for the user.
     * [SPECGT20]
     * @return void
     */
    public function getScheduleEvents(): void
    {
        try {
            $this->events = $this->groupEventsByDay($this->user->times);
        } catch (\Exception $e) {
            $this->hasError = "Une erreur s'est produite lors de la récupération des événements. Veuillez nettoyer la base de données.";
            dd($e->getMessage());
        }
    }

    /**
     * Handle the availability event.
     * [SPECGT20]
     * @param array $data
     * @return void
     */
    public function handleAvailability($data)
    {
        $date = $data['date'];
        isset($data['end']) ? $end = $data['end'] : $end = null;
        $hours = $data['dayHours'];
        $stateId = $data['stateId'];
        $time = $this->calculAvailability($date, $end, $hours, $stateId);
        $this->emit('availabilityProcessed', $time);
    }

    /**
     * Handle the delete event.
     * [SPECGT20]
     * @param int $id
     * @return void
     */
    public function handleDeleteEvent($id)
    {
        Time::destroy($id);
        $this->emit('eventDeleted', $id);
    }

    /**
     * Handle the calculation of the week list hours.
     * [SPECGT26]
     * @param array $data
     * @return void
     */
    public function handleCalculWeekListHours($data): void
    {
        $start = $data['start'];
        $end = $data['end'];
        $this->calculService->setUser($this->user);
        $dayHours = $this->calculService->weekProductiveHoursOnDay($start, $end);
        $nightHours = $this->calculService->weekProductiveHoursOnNight($start, $end);
        $travelsHours = $this->calculService->weekHoursOnTravel($start, $end);
        $unproductiveHours = $this->calculService->weekNonProductiveHoursOnDay($start, $end) + $this->calculService->weekNonProductiveHoursOnNight($start, $end);
        $this->emit('onWeekListHoursCalculated', [
            'dayHours' => $dayHours ?? 0,
            'nightHours' => $nightHours ?? 0,
            'travelsHours' => $travelsHours ?? 0,
            'unproductiveHours' => $unproductiveHours ?? 0,
        ]);
    }

    /**
     * Handle the calculation of the monthly hours.
     * [SPECGT26]
     * @param array $data
     * @return void
     */
    public function handleCalculMonthlyHours($data): void
    {
        $travelsHours = $this->calculService->monthlyTravelsHours($data['start']);
        $travelEventsDays = [];
        if ($travelsHours > 0) {
            $travelEventsDays = $this->calculService->monthlyTravelEventsDays($data['start']);
        }
        $this->emit('onMonthlyHoursCalculated', [
            'dayHours' => $this->calculService->monthlyDayHours($data['start']),
            'nightHours' => $this->calculService->monthlyNightHours($data['start']),
            'travelsHours' => $travelsHours,
            'travelEventsDays' => $travelEventsDays,
        ]);
    }

    /**
     * Render the component.
     * @return View
     */
    public function render(): View
    {
        return view('livewire.user-schedule-manager');
    }

    /**
     * Group the events by day, calcul the hours and return the final events array.
     * [SPECGT20] - The function handle everything itself because the database isn't clean yet (2024/04/25)
     * @param array $eventsData
     * @return array
     */
    private function groupEventsByDay($eventsData): array
    {
        $finalEvents = [];
        $groupedEvents = [];
        $availabilityTimes = [];
        $businessTrips = [];
        $workTimes = [];

        // Breakdown the events into availability and work times
        foreach ($eventsData as $time) {
            if ($time->isState()) {
                $availabilityTimes[] = $time;
            } elseif ($time->isBusinessTrip()) {
                $businessTrips[] = $time;
            } else {
                $workTimes[] = $time;
            }
        }

        // Handling availability times
        foreach ($availabilityTimes as $time) {
            $finalEvents[] = [
                'title' => $time->getStateLabel(),
                'start' => $time->date,
                'id' => $time->id,
                'dayHours' => $time->hours_day ?? 0,
                'allDay' => true,
                'textColor' => 'white',
                'backgroundColor' => $time->getStateColor(),
                'note' => $time->note ?? '',
                'className' => 'text-center',
                'workEvent' => false,
                'isAvaibility' => true,
                'oncallDuty' => false,
                'onBusinessTrip' => false,
                'nightEvent' => false,
                'dayViewTitle' => $time->getStateLabel(),
                'display' => 'block',
            ];
        }

        foreach ($businessTrips as $time) {
            $finalEvents[] = [
                'title' => 'Grand Déplacement',
                'start' => $time->date,
                'id' => $time->id,
                'allDay' => true,
                'textColor' => 'white',
                'backgroundColor' => '#A52A2A',
                'note' => $time->note ?? '',
                'className' => 'text-center',
                'workEvent' => false,
                'isAvaibility' => false,
                'oncallDuty' => false,
                'onBusinessTrip' => true,
                'nightEvent' => false,
                'dayViewTitle' => 'Grand Déplacement',
                'display' => 'block',
            ];
        }

        // Handling work times
        foreach ($workTimes as $time) {
            if (!$time->oncall_duty) {
                $date = $time->date;
                // Initialise the grouped events for this date if they don't exist yet
                if (!isset($groupedEvents[$date])) {
                    $groupedEvents[$date] = [];
                    $groupedEvents[$date]['ids'] = [];
                }
                if (!isset($groupedEvents[$date]['dayHours'])) $groupedEvents[$date]['dayHours'] = [];
                if (!isset($groupedEvents[$date]['nightHours'])) $groupedEvents[$date]['nightHours'] = [];
                if (!isset($groupedEvents[$date]['travelsHours'])) $groupedEvents[$date]['travelsHours'] = [];
                if (!isset($groupedEvents[$date]['note'])) $groupedEvents[$date]['note'] = '';

                // Add the work hours to the dayHours and nightHours arrays
                if ($time->isDayHours()) {
                    $groupedEvents[$date]['dayHours'][] = $time->hours_day;
                }
                if ($time->isNightHours()) {
                    $groupedEvents[$date]['nightHours'][] = $time->hours_night;
                }
                if ($time->isTravelHours()) {
                    $groupedEvents[$date]['travelsHours'][] = $time->hours_travel;
                }

                // Add the note to the note array
                if ($time->note) {
                    $groupedEvents[$date]['note'] .= $time->note . ' ';
                }

                if (!in_array($time->id, $groupedEvents[$date]['ids'])) {
                    $groupedEvents[$date]['ids'][] = $time->id;
                }
                if (!$time->hours_travel && !$time->hours_day && !$time->hours_night && $time->note) {
                    $groupedEvents[$date]['display'] = 'none';
                }else{
                    $groupedEvents[$date]['display'] = 'block';
                }
            } else {
                $finalEvents[] = [
                    'title' => 'Astreinte',
                    'start' => $time->date,
                    'id' => $time->id,
                    'allDay' => true,
                    'textColor' => 'white',
                    'backgroundColor' => '#ed8936',
                    'note' => $time->note ?? '',
                    'className' => 'text-center',
                    'workEvent' => false,
                    'isAvaibility' => false,
                    'oncallDuty' => true,
                    'onBusinessTrip' => false,
                    'nightEvent' => false,
                    'dayViewTitle' => 'Astreinte',
                    'display' => 'block',
                ];
            }
        }

        // Create the final events array
        foreach ($groupedEvents as $date => &$events) {

            // Calculate the total day and night hours for this date
            $totalDayHours = isset($events['dayHours']) ? array_sum($events['dayHours']) : 0;
            $totalNightHours = isset($events['nightHours']) ? array_sum($events['nightHours']) : 0;
            $totalTravelHours = isset($events['travelsHours']) ? array_sum($events['travelsHours']) : 0;
            $totalHours = null;

            // Merge the dayHours and nightHours arrays into the final events array
            if ($totalDayHours > 0) $totalHours += $totalDayHours;
            if ($totalNightHours > 0) $totalHours += $totalNightHours;
            if ($totalTravelHours > 0) $totalHours += $totalTravelHours;

            // Add night event to show which days are due.
            if ($totalNightHours > 0) {
                $datetime = new DateTime($date);
                $datetime->setTime($totalNightHours, 0);
                $ISODate = $datetime->format(DateTime::ATOM);
                $finalEvents[] = [
                    'title' => 'Heures de nuit',
                    'start' => $ISODate,
                    'id' => $events['ids'][0],
                    'backgroundColor' => '#f8f9a',
                    'allDay' => false,
                    'className' => 'text-center',
                    'ids' => $events['ids'],
                    'nightHours' => $totalNightHours,
                    'workEvent' => false,
                    'isAvaibility' => false,
                    'nightEvent' => true,
                    'oncallDuty' => false,
                    'onBusinessTrip' => false,
                    'dayViewTitle' => 'Heures de nuit',
                    'display' => 'block',
                ];
            }

            if ($totalTravelHours > 0) {
                $datetime = new DateTime($date);
                $datetime->setTime($totalTravelHours, 0);
                $ISODate = $datetime->format(DateTime::ATOM);
                $finalEvents[] = [
                    'title' => 'Heures de trajet',
                    'start' => $ISODate,
                    'id' => $events['ids'][0],
                    'backgroundColor' => '#f8f9a',
                    'allDay' => false,
                    'className' => 'text-center',
                    'ids' => $events['ids'],
                    'travelHours' => $totalTravelHours,
                    'workEvent' => false,
                    'isAvaibility' => false,
                    'nightEvent' => false,
                    'oncallDuty' => false,
                    'onBusinessTrip' => false,
                    'dayViewTitle' => 'Heures de trajet',
                    'display' => 'block',
                ];
            }

            $finalEvents[] = [
                'title' => 'Total des heures : ' . Time::formatWorkHours($totalHours),
                'start' => $date,
                'id' => $events['ids'][0],
                'allDay' => true,
                'textColor' => 'white',
                'backgroundColor' => $time->getStateColor(),
                'note' => $events['note'],
                'className' => 'text-center',
                'ids' => $events['ids'],
                'dayHours' => $totalDayHours,
                'nightHours' => $totalNightHours,
                'travelHours' => $totalTravelHours,
                'workEvent' => true,
                'isAvaibility' => false,
                'nightEvent' => false,
                'oncallDuty' => false,
                'onBusinessTrip' => false,
                'dayViewTitle' => 'Total des heures : ' . Time::formatWorkHours($totalHours),
                'weekViewTitle' => 'H-jour : ' . Time::formatWorkHours($totalDayHours) . ' | H-nuit : ' . Time::formatWorkHours($totalNightHours) . ' | H-trajet : ' . Time::formatWorkHours($totalTravelHours),
                'display' => $events['display'],
            ];
            // Delete the dayHours and nightHours arrays to avoid duplicates
            unset($events['dayHours'], $events['nightHours'], $events['travelHours']);

            // dd($finalEvents);
        }
        return $finalEvents;
    }

    /**
     * Calculate the availability of the user.
     * [SPECGT20]
     * @param string $date
     * @param int $hours
     * @param int $stateId
     * @return array
     */
    private function calculAvailability($date, $end, $hours, $stateId): array
    {
        if (isset($end)) {
            return (array) Time::createUserAvailabilityForMultipleDays($this->user->id, $hours, $date, $end, $stateId);
        } else {
            $time = Time::isAvailabilityDeclared($this->user->id, $date) ?
                Time::updateUserAvailability($this->user->id, $hours, $date, $stateId)
                : Time::createUserAvailability($this->user->id, $hours, $date, $stateId);
            return  [[
                'title' => $time->getStateLabel(),
                'start' => $time->date,
                'id' => $time->id,
                'allDay' => true,
                'textColor' => 'white',
                'backgroundColor' => $time->getStateColor(),
                'note' => $time->note ?? '',
                'className' => 'text-center',
                'isAvaibility' => true,
                'dayHours' => $time->hours_day ?? 0,
            ]];
        }
    }
}
// FIN [SPECGT20] - Mise à jour de la fonctionnalité de suivi des heures
