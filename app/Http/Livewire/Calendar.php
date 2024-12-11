<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;
use App\Models\User;
use App\Models\Time;
use App\Models\Chantier;
use App\Models\Message;
use Illuminate\Support\Arr;

class Calendar extends Component
{
    public $events = [];
    public $totalMaterialAmount;
    public $totalServiceAmount;
    public $totalHoursScheduled;
    public $totalHoursDone;
    public $totalHourlyRate;

    public function mount()
    {
        $this->totalMaterialAmount = 0;
        $this->totalServiceAmount = 0;
        $this->totalHoursScheduled = 0;
        $this->totalHoursDone = 0;
        $this->totalHourlyRate = 0;
    }

    public function eventChange($id, $event)
    {
        $e = Event::find($id);
        $e->start = $event['start'];
        if (Arr::exists($event, 'end')) {
            $e->end = $event['end'];
        }
        $e->save();
    }

    public function eventAdd($event, $id, $id_chantier)
    {
        $event = new Event([
            'id' => $id,
            'title' => $event['title'],
            'start' => $event['start'],
            'id_chantier' => $id_chantier
        ]);

        $event->save();
    }

    public function eventRemove($id)
    {
        Event::destroy($id);
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
     * Partition work sites by hours done
     * [SPECGT10] Partitionner les chantiers par heures effectuées
     * @param $workSites
     * @return mixed
     */
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

    public function render()
    {
        $this->events = json_encode(Event::with(['user', 'chantier'])->get());
        $chantiers = Chantier::all()->load('loadout');

        $users = User::all();
        $message = Message::whereIsPublished(1)->orderBy('id', 'desc')->get();

        // Début [SPECGT10][V2.1] - Récupération des chantiers en fonction du statut
        $workSitesByState = $chantiers->groupBy(function ($chantier) {
            return $chantier->states->status_group;
        });

        $inProgressWorkSites = $workSitesByState['inProgress'] ?? [];
        list($startedWorkSites, $upcomingWorkSites) = $this->partitionWorkSitesByHoursDone($inProgressWorkSites);

        $toBillWorkSites = $workSitesByState['toBill'] ?? [];
        $archivedWorkSites = $workSitesByState['archived'] ?? [];
        // Fin [SPECGT10][V2.1] - Récupération des chantiers en fonction du statut

        // [SPECGT10] Calcul du total des heures affectées
        $this->totalMaterialAmount = 0;
        $this->totalServiceAmount = 0;
        $this->totalHoursScheduled = 0;
        $this->totalServiceAmount = 0;
        $this->totalHourlyRate = 0;
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
        $this->totalHourlyRate = number_format($globalTotals['totalServiceAmount'] / $globalHoursDone, 2, '.', '');
        $averageToBillHourlyRate = number_format($toBillWorkSitesTotals['totalServiceAmount'] / $toBillWorkSitesTotals['totalHoursScheduled'], 2, '.', '');
        $averageUpcomingHourlyRate = number_format($upcomingWorkSitesTotals['totalServiceAmount'] / $upcomingWorkSitesTotals['totalHoursScheduled'], 2, '.', '');
        $averageStartedHourlyRate = number_format($startedWorkSitesTotals['totalServiceAmount'] / $startedWorkSitesTotals['totalHoursScheduled'], 2, '.', '');
        
        $totalAmountFromHoursDone = $this->totalHoursDone * $this->totalHourlyRate;
        $totalAmountFromToBillHourlyRate = $this->totalHoursDone * $averageToBillHourlyRate;
        $totalAmountFromUpcomingHourlyRate = $this->totalHoursDone * $averageUpcomingHourlyRate;

        return view('livewire.calendar', compact(
            'chantiers',
            'users',
            'message',
            'startedWorkSites',
            'upcomingWorkSites',
            'toBillWorkSites',
            'startedWorkSitesTotals',
            'upcomingWorkSitesTotals',
            'toBillWorkSitesTotals',
            'archivedWorkSites',
            'averageToBillHourlyRate',
            'averageUpcomingHourlyRate',
            'averageStartedHourlyRate',
            'totalAmountFromHoursDone',
            'totalAmountFromToBillHourlyRate',
            'totalAmountFromUpcomingHourlyRate'
        ))->with('totalHours', $this->totalHoursDone);
        // Fin [SPECGT10][V2.1] - Calcul des totaux par statut & modification du return
    }
}
