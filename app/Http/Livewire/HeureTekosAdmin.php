<?php

namespace App\Http\Livewire;
use App\Models\Chantier;
use App\Models\Event;
use App\Models\Time;
use Livewire\Component;

class HeureTekosAdmin extends Component
{
    public $userId;
    public $chantiers;
    public $times;
    public $events;
    public $selectedDate;


    public function render()
    {
        $this->events = json_encode(Event::with('user')->get());
        dd($this->events);
        $this->times = json_encode(Time::with('user')->get());
        $this->chantiers = Chantier::all();

    return view('livewire.heure-tekos-admin', [
            'chantiers' => $this->chantiers,
            'times' => $this->times,
            'events' => $this->events,
            'selectedDate' => $this->selectedDate,
        ]);
}
}
