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
  public string $events = '';

  public function mount() {}

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

  public function addAstreinte($date, $userId)
  {
    // Convertir la date en objet Carbon
    $startDate = \Carbon\Carbon::parse($date);

    // Ajuster au lundi de la semaine si ce n'est pas déjà un lundi
    $monday = $startDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);

    // Créer un événement pour chaque jour de la semaine (lundi à vendredi)
    for ($i = 0; $i < 7; $i++) {
      $currentDate = $monday->copy()->addDays($i);

      $time = new Time();
      $time->date = $currentDate->format('Y-m-d');
      $time->user_id = $userId;
      $time->oncall_duty = 1;
      $time->hours_day = 7;
      $time->hours_night = 0;
      $time->hours_travel = 0;

      $time->save();
    }

    $this->emit('astreinteAdded');
  }

  public function updateAstreinte($id, $userId)
  {
    // Trouver l'astreinte existante
    $astreinte = Time::find($id);
    if (!$astreinte) {
      return;
    }

    // Convertir la date en objet Carbon
    $startDate = \Carbon\Carbon::parse($astreinte->date);
    $monday = $startDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);

    // Mettre à jour toutes les astreintes de la semaine avec le nouveau technicien
    Time::where('oncall_duty', 1)
      ->whereBetween('date', [
        $monday->format('Y-m-d'),
        $monday->copy()->addDays(4)->format('Y-m-d')
      ])
      ->update(['user_id' => $userId]);

    $this->emit('astreinteUpdated');
  }

  public function deleteAstreinte($id)
  {
    // Trouver l'astreinte existante
    $astreinte = Time::find($id);
    if (!$astreinte) {
      return;
    }

    // Convertir la date en objet Carbon
    $startDate = \Carbon\Carbon::parse($astreinte->date);
    $monday = $startDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);

    // Supprimer toutes les astreintes de la semaine
    Time::where('oncall_duty', 1)
      ->whereBetween('date', [
        $monday->format('Y-m-d'),
        $monday->copy()->addDays(4)->format('Y-m-d')
      ])
      ->delete();

    $this->emit('astreinteDeleted');
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

    $archivedWorkSites = $workSitesByState['archived'] ?? [];

    $public_holidays = Time::where('state', 5)
      ->select('*')
      ->whereIn('id', function ($query) {
        $query->selectRaw('MIN(id)')
          ->from('times')
          ->where('state', 5)
          ->groupBy('date');
      })
      ->get();

    $oncall_duty = Time::with('user')->where('oncall_duty', 1)->get();

    foreach ($public_holidays as $public_holiday) {
      $public_holiday->display = 'background';
    }

    $events = collect(json_decode($this->events));
    $mergedEvents = $events->merge($public_holidays);
    $mergedEvents = $mergedEvents->merge($oncall_duty);
    $this->events = json_encode($mergedEvents);
    return view('livewire.calendar', compact(
      'chantiers',
      'users',
      'message',
      'archivedWorkSites',
      'public_holidays'
    ));
  }
}
