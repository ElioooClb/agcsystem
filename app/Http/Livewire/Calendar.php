<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;
use App\Models\User;
use App\Models\Time;
use App\Models\Chantier;
use App\Models\Message;
use Illuminate\Support\Arr;
use App\Models\CustomEvent;

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

    // Supprimer uniquement les astreintes de l'utilisateur spécifique pour la semaine
    Time::where('oncall_duty', 1)
      ->where('user_id', $astreinte->user_id)
      ->whereBetween('date', [
        $monday->format('Y-m-d'),
        $monday->copy()->addDays(4)->format('Y-m-d')
      ])
      ->delete();

    $this->emit('astreinteDeleted');
  }

  public function addCustomEvent($date, $title, $backgroundColor = '#3788d8', $textColor = '#ffffff', $description = '')
  {
    // On force le fuseau horaire à Paris pour éviter les décalages
    $date = \Carbon\Carbon::parse($date)->setTimezone('Europe/Paris');

    $customEvent = CustomEvent::create([
      'title' => $title,
      'start' => $date->startOfDay(),
      'end' => $date->endOfDay(),
      'allDay' => 1,
      'backgroundColor' => $backgroundColor,
      'borderColor' => $backgroundColor,
      'textColor' => $textColor,
      'extendedProps' => [
        'description' => $description
      ]
    ]);

    $this->emit('customEventAdded');
    $this->emit('flashMessage', 'success', 'Événement personnalisé ajouté avec succès');
  }

  public function updateCustomEvent($id, $title, $backgroundColor = null, $description = '')
  {
    $customEvent = CustomEvent::findOrFail($id);
    $updates = [
      'title' => $title,
      'extendedProps' => [
        'description' => $description
      ]
    ];

    if ($backgroundColor) {
      $updates['backgroundColor'] = $backgroundColor;
      $updates['borderColor'] = $backgroundColor;
    }

    $customEvent->update($updates);

    $this->emit('customEventUpdated');
    $this->emit('flashMessage', 'success', 'Événement personnalisé mis à jour avec succès');
  }

  public function deleteCustomEvent($id)
  {
    $customEvent = CustomEvent::findOrFail($id);
    $customEvent->delete();

    $this->emit('customEventDeleted');
    $this->emit('flashMessage', 'success', 'Événement personnalisé supprimé avec succès');
  }

  public function getEvents()
  {
    $events = [];

    // Ajouter les événements personnalisés
    $customEvents = CustomEvent::all();
    foreach ($customEvents as $event) {
      $events[] = [
        'id' => 'custom_' . $event->id,
        'title' => $event->title,
        'start' => $event->start,
        'end' => $event->end,
        'allDay' => $event->allDay,
        'backgroundColor' => $event->backgroundColor,
        'borderColor' => $event->borderColor,
        'textColor' => $event->textColor,
        'url' => $event->url,
        'extendedProps' => $event->extendedProps,
        'isCustomEvent' => true
      ];
    }

    return $events;
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

    foreach ($oncall_duty as $ocd) {
      $ocd->allDay = true;
      $ocd->backgroundColor = '#f97316';
    }

    foreach ($public_holidays as $public_holiday) {
      $public_holiday->display = 'background';
    }

    $customEvents = $this->getEvents();

    $events = collect(json_decode($this->events));
    $mergedEvents = $events->merge($public_holidays);
    $mergedEvents = $mergedEvents->merge($oncall_duty);
    $mergedEvents = $mergedEvents->merge($customEvents);
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
