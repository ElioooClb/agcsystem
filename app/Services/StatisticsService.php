<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\Time;
use App\Models\User;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StatisticsService
 * @package App\Services
 * @version 1.0 [SPECMBA06]
 */
class StatisticsService
{
    protected DateTime $start;
    protected DateTime $end;
    protected Collection $timesBetweenDates;
    protected $users;
    protected $usersIDs;
    protected $worksites;
    protected Collection $productiveHours;
    protected Collection $unproductiveHours;

    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Get the data that have declarations between the given dates
     * @param DateTime $start
     * @param DateTime $end
     * @return array
     * @version 1.0 [SPECMBA06]
     */
    public function initData(DateTime $start, DateTime $end): void
    {
        $this->productiveHours = new Collection();
        $this->unproductiveHours = new Collection();
        $this->users = new Collection();
        $this->usersIDs = [];
        $this->start = $start;
        $this->end = $end;

        // Worksite qui ont des déclarations (times where chantier_id != null et hours_(...) > 0)
        $this->timesBetweenDates = Time::whereBetween('date', [$start, $end])
            ->whereNotNull('chantier_id')
            ->where(function ($query) {
                $query->where('hours_day', '>', 0)
                    ->orWhere('hours_night', '>', 0)
                    ->orWhere('hours_travel', '>', 0);
            })
            ->get();

        // Extracting the worksites IDs (unique)
        $worksitesIDs = $this->timesBetweenDates->pluck('chantier_id')->unique();

        // Get the worksites by IDs
        $this->worksites = Chantier::whereIn('id', $worksitesIDs)
            ->with('times')
            ->get();

        // Sort the times by productive and unproductive
        $this->mountProductiveAndUnproductiveHours();

        // Extracting the users IDs (unique)
        $this->usersIDs = $this->timesBetweenDates->pluck('user_id')->unique();

        // Get the users by IDs
        $this->initUsers($start, $end);

        // Initialize the progress of the worksites
        $this->mountProgressAndMoe();
    }

    /**
     * Get the worksites that have declarations between the given dates
     * @return array
     * @version 1.0 [SPECMBA06]
     */
    public function getWorksites(): Collection
    {
        return $this->worksites;
    }

    /**
     * Get the total material amount between the given dates
     * @return int
     */
    public function calculTotalMaterialAmount(): int
    {
        return $this->worksites->sum('materialamount');
    }

    /**
     * Get the total revenue between the given dates
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    public function calculTotalRevenue(): int
    {
        return $this->worksites->sum('moe');
    }

    /**
     * Get the potential hours between the given dates
     * @return int|float
     * @version 1.0 [SPECMBA06]
     */
    public function calculPotentialHours(): int
    {
        $potentialHours = 0;

        // Calcultate the number of working days between the start and the end
        $workingDays = $this->getBusinessDays($this->start, $this->end);

        // Calculate the user coefficient
        $coef = $this->getCoefficient();

        // Calculate the potential hours (working days * 7 hours * users coefficients)
        $potentialHours = (int) round($workingDays * 7 * $coef);

        Log::info('User list : ' . $this->users);

        // Substract the hours where the user is not available
        foreach ($this->users as $user) {
            if ($user->coef_prod > 0) {
                $numberOfAvailabilities = Time::where('user_id', $user->id)
                    ->whereBetween('date', [$this->start, $this->end])
                    ->where(function ($query) {
                        $query->where('state', 1)
                            ->orWhere('state', 3)
                            ->orWhere('state', 4)
                            ->orWhere('state', 5);
                    })
                    ->whereRaw('DAYOFWEEK(date) NOT IN (1, 7)') // Exclut dimanche (1) et samedi (7)
                    ->count();
                $potentialHours -= $numberOfAvailabilities * 7;
            }
        }
        return $potentialHours;
    }

    /**
     * Get unproductive hours between the given dates
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    public function calculPeriodUnproductiveHours(): int
    {
        return Time::whereBetween('date', [$this->start, $this->end])
            ->whereIn('user_id', $this->usersIDs)
            ->whereNull('chantier_id')
            ->whereNull('state')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum(DB::raw('hours_day + hours_night + hours_travel'));
    }

    /**
     * Get unproductive hours between the given dates for all users
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    public function calculAllUsersPeriodUnproductiveHours(): int
    {
        return Time::whereBetween('date', [$this->start, $this->end])
            ->whereNull('chantier_id')
            ->whereNull('state')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum(DB::raw('hours_day + hours_night + hours_travel'));
    }

    /**
     * Get the consumed hours between the given dates
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    public function calculPeriodProductiveHours(): int
    {
        return $this->worksites->sum('periodProductiveHours');
    }

    public function calculUnbillableHours(): int
    {
        return Time::whereBetween('date', [$this->start, $this->end])
            ->whereNull('chantier_id')
            ->whereNull('state')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->where('unbillable', 1)
            ->sum(DB::raw('hours_day + hours_night + hours_travel'));
    }

    /**
     * Get the total consumed hours for a worksite
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    public function calculTotalProductiveHours(): int
    {
        return $this->worksites->sum('consumedHours');
    }

    /**
     * Get the total hours between the given dates
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    public function calculTotalHours(): int
    {
        return $this->calculPeriodProductiveHours() + $this->calculPeriodUnproductiveHours();
    }

    /**
     * Initialize the progress and labor value for the worksites
     * @version 1.0 [SPECMBA06]
     */
    private function mountProgressAndMoe(): void
    {
        foreach ($this->worksites as $worksite) {
            $progress = 0;
            $consumedhours = $worksite->periodProductiveHours;
            if ($worksite->revised_hours && $worksite->revised_hours > 0) {
                $progress = round(($consumedhours / $worksite->revised_hours), 2);
            }
            $worksite->progress = $progress;
            $worksite->moe = $progress * $worksite->serviceamount;
        }
    }

    /**
     * Sort the times by productive and unproductive
     * @version 1.0 [SPECMBA06]
     */
    private function mountProductiveAndUnproductiveHours(): void
    {
        foreach ($this->worksites as $worksite) {
            // Les times entre deux dates
            $worksite->periodProductiveHours = Time::whereBetween('date', [$this->start, $this->end])
                ->where('chantier_id', '=', $worksite->id)
                ->where(function ($query) {
                    $query->where('hours_day', '>', 0)
                        ->orWhere('hours_night', '>', 0)
                        ->orWhere('hours_travel', '>', 0);
                })
                ->sum(DB::raw('hours_day + hours_night + hours_travel'));

            // TOUT les times depuis son existence
            foreach ($worksite->times as $time) {
                if ($time->state == null && $time->oncall_duty == null && $time->on_business_trip == null) {
                    if ($time->chantier_id != null) {
                        $worksite->totalConsumedHours += $time->hours_day + $time->hours_night + $time->hours_travel;
                    }
                }
            }
        }
    }

    /**
     * Get the worksites that have declarations between the given dates
     * @param DateTime $start
     * @param DateTime $end
     * @return int
     * @version 1.0 [SPECMBA06]
     */
    private function getBusinessDays($startDate, $endDate): int
    {
        // Ajouter un jour à la date de fin pour inclure le dernier jour
        $endDateInclusive = (clone $endDate)->modify('+1 day');

        // Créer une intervalle d'un jour à partir de la date de début
        $interval = new DateInterval('P1D');

        // Créer une période de dates entre start et end inclusivement
        $period = new DatePeriod($startDate, $interval, $endDateInclusive);

        $businessDaysCount = 0;

        // Itérer sur chaque jour de la période
        foreach ($period as $date) {
            // Vérifier si le jour est un jour ouvrable (lundi à vendredi)
            if ($date->format('N') <= 5) {
                $businessDaysCount++;
            }
        }

        return $businessDaysCount;
    }

    /**
     * Get the coefficient of the users between the given dates
     * @return int|float
     * @version 1.0 [SPECMBA06]
     */
    private function getCoefficient(): int|float
    {
        return $this->users->sum(function ($user) {
            return $user->coef_prod / 100;
        });
    }

    /**
     * Initialize the users by IDs
     * @param array $usersIDs
     * @param DateTime $start
     * @param DateTime $end
     * @version 1.0 [SPECMBA06]
     */
    private function initUsers($start, $end): void
    {
        $this->users = new Collection();
        $this->users = User::whereIn('id', $this->usersIDs)
            ->whereHas('times', function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })
            ->get();
    }
}
