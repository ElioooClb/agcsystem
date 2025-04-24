<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Time extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'repas',
        'date',
        'hours_day',
        'hours_night',
        'hours_travel',
        'user_id',
        'chantier_id',
        'note',
    ];

    // DEBUT [SPECGT20] - Ajout d'attributs compilés
    private array $dayState = [
        '1' => 'Congé payé',
        '2' => 'Récupération',
        '3' => 'Arrêt',
        '4' => 'Absence',
        '5' => 'Férié',
    ];

    private array $colorState = [
        ''  => '#FF99FF',
        '1' => '#22C55E',
        '2' => '#EAB308',
        '3' => '#3B82F6',
        '4' => '#A855F7',
        '5' => '#a0aec0',
    ];

    /**
     * Get the truthy value of the day hours attribute.
     * [SPECGT20]
     * @return bool
     */
    public function isDayHours(): bool
    {
        return $this->hours_day > 0;
    }

    /**
     * Get the truthy value of the night hours attribute.
     * [SPECGT20]
     * @return bool
     */
    public function isNightHours(): bool
    {
        return $this->hours_night > 0;
    }

    /**
     * Get the truthy value of the travel hours attribute.
     * [SPECGT24]
     * @return bool
     */
    public function isTravelHours(): bool
    {
        return $this->hours_travel > 0;
    }

    /**
     * Get the truthy value of the state attribute.
     * [SPECGT20]
     * @return bool
     */
    public function isState(): bool
    {
        return array_key_exists($this->state, $this->dayState);
    }

    /**
     * Get the truthy value of the hours travel attribute.
     * [SPECMBA04]
     * @return bool
     */
    public function isHoursTravel(): bool
    {
        return $this->hours_travel > 0;
    }

    /**
     * Get the truthy value of the on_business_trip duty attribute.
     * [SPECMBA05]
     * @return bool
     */
    public function isBusinessTrip(): bool
    {
        return $this->on_business_trip === 1;
    }

    /**
     * Get the truthy value of the oncall duty attribute.
     * [SPECGT26]
     * NB: This method should be handled by the state table or cast on the model.
     * @return bool
     */
    public function isOncallDuty(): bool
    {
        return $this->oncall_duty === 1;
    }

    /**
     * Get the label of the state attribute.
     * [SPECGT20]
     * NB: This method should be handled by the state table.
     * @return string
     */
    public function getStateLabel(): string
    {
        return $this->dayState[$this->state];
    }

    /**
     * Get the color of the state attribute.
     * [SPECGT20]
     * NB: This method should be handled by the state table.
     * @return string
     */
    public function getStateColor(): string
    {
        return $this->colorState[$this->state];
    }

    /**
     * Check if the availability is declared for a given date.
     * [SPECGT20]
     * @param int $userId
     * @param string $date
     * @return bool
     */
    static public function isAvailabilityDeclared(int $userId, string $date): bool
    {
        return Time::where('date', $date)->where('user_id', $userId)->whereNotNull('state')->where('on_business_trip', 0)->exists();
    }

    /**
     * Create an availability for a given date.
     * [SPECGT20]
     * @param string $hours
     * @param string $date
     * @param int $stateId
     * @return Time
     */
    static public function createUserAvailability(int $userId, string $hours, string $date, int $stateId): Time
    {
        if ($hours == "00:00") $hours = "07:00";
        $time = new Time;
        $time->user_id = $userId;
        $time->date = $date;
        $time->hours_day = (int)explode(':', $hours)[0] + (int)explode(':', $hours)[1] / 60;
        $time->state = $stateId;
        $time->save();
        return $time;
    }

    /**
     * Update the availability for a given date.
     * [SPECGT20]
     * @param int $userId
     * @param string $hours
     * @param string $date
     * @param int $stateId
     * @return Time
     */
    static public function updateUserAvailability(int $userId, string $hours, string $date, int $stateId): Time
    {
        $time = Time::where('user_id', $userId)->where('date', $date)->whereNotNull('state')->first();
        $time->state = $stateId;
        $time->hours_day = (int)explode(':', $hours)[0] + (int)explode(':', $hours)[1] / 60;
        $time->save();
        return $time;
    }

    /**
     * Create an event per day for multiple days.
     * [SPECGT20]
     * @param int $userId
     * @param string $date
     * @param int $stateId
     * @return void
     */
    static public function createUserAvailabilityForMultipleDays(int $userId, string $hours, string $start, string $end, int $stateId): array
    {
        $date = $start;
        $result = [];
        while ($date <= $end) {
            $time = Time::isAvailabilityDeclared($userId, $date) ?
                Time::updateUserAvailability($userId, $hours, $date, $stateId)
                : Time::createUserAvailability($userId, $hours, $date, $stateId);
            $result[] = [
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
            ];
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        }
        return $result;
    }
    /**
     * Get the user that owns the time.
     * [SPECGT20]
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // FIN [SPECGT20] - Ajout d'un attributs compilés

    // Ajout d'une relation entre les tables chantiers et times
    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }

    /**
     * Format the work hours to a string to '00h00'.
     * [SPECGT20]
     * @param int $number
     * @return string
     */
    static public function formatWorkHours($number): string
    {
        // Get the hours and minutes from the number
        $hours = floor($number);
        $minutes = round(($number - $hours) * 60);

        // Format the hours and minutes with leading zeros if necessary
        $hoursString = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutesString = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        return $hoursString . 'h' . $minutesString;
    }
}
