<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chantier;
use Illuminate\Support\Facades\Log;
class WorksiteStageUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $worksite;
    public $oldStage;
    public $newStage;
    public $direction;

    /**
     * Create a new event instance.
     */
    public function __construct(Chantier $worksite, string $oldStage, string $newStage, string $direction)
    {
        $this->worksite = $worksite;
        $this->oldStage = $oldStage;
        $this->newStage = $newStage;
        $this->direction = $direction;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
