<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;

use Illuminate\Support\Facades\Auth;

class Heures extends Component
{
    public int $userId;
    public $times;
    public $chantiers;
    public $chantierIds;
    public $productiveHours;
    public $nonProductiveHours;
    public $events;

    /**
     * Fast patchwork to prevent unauthorized access to the page
     * Better to use a middleware (rework needed)
     * [SPECGT4] - Protection contre la falscification des heures de travail d'un autre utilisateur
     */
    public function mount($userId, $initialTimes, $chantiers, $initialProductiveHours, $initialNonProductiveHours)
    {
        if (Auth::check()) {
            $testId = Auth::user()->id;
            $role = Auth::user()->role;
            $requestedUserId = request()->route('user')->id;

            if ($testId !== $requestedUserId) {
                if ($role->id !== 1) {
                    abort(403, 'Unauthorized action.');
                }
            }
        }

        $this->userId = $userId;
        $this->times = $initialTimes;
        $this->chantiers = $chantiers;
        $this->chantierIds = $chantiers->pluck('id')->toArray();
        $this->productiveHours = $initialProductiveHours;
        $this->nonProductiveHours = $initialNonProductiveHours;
        $this->events = Event::with('user')->get();
    }

    public function render()
    {
        return view('livewire.heures', [
            'chantiers' => json_encode($this->chantiers),
            'chantierIds' => json_encode($this->chantierIds),
            'times' => json_encode($this->times),
            'events' => json_encode($this->events),
            'productiveHours' => $this->productiveHours,
            'nonProductiveHours' => $this->nonProductiveHours
        ]);
    }
}
