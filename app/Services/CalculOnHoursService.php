<?php

namespace App\Services;

use App\Models\Time;
use App\Models\User;
use App\Models\Chantier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service to calculate hours on the user's schedule.
 * date: 2024/04/25
 * [SPECGT26]
 */
class CalculOnHoursService
{
    // DEBUT [SPECGT26] - Création de la classe de service pour le calcul des heures
    private User $user;

    /**
     * Initialize the service with the user.
     * @param User $user
     * @return void
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Get the day billable day hours for the user.
     * @param string $date date format 'Y-m-d'
     * @return float
     */
    public function dayProductiveHoursOnDay(string $date): float
    {
        return Time::where('date', $date)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum('hours_day');
    }

    /**
     * Get the day productive night hours for the user.
     * @param string $date date format 'Y-m-d'
     * @return float
     */
    public function dayProductiveHoursOnNight(string $date): float
    {
        return Time::where('date', $date)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum('hours_night');
    }

    /**
     * [SPECGT24] - Ajouts des heures de trajet au calcul des heures
     * Get the day billable hours for the user.
     * @param string $date date format 'Y-m-d'
     * @return float
     */
    public function dayProductiveHours(string $date): float
    {
        return Time::where('date', $date)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum(DB::raw('hours_day + hours_night + hours_travel'));
    }

    /**
     * [SPECGT25] - Retrait des heures d'astreinte du calcul des heures non productives
     * Get the day non-billable hours for the user.
     * @param string $date date format 'Y-m-d'
     * @return float
     */
    public function dayNonProductiveHours(string $date): float
    {
        return Time::where('date', $date)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNull('chantier_id')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum(DB::raw('hours_day + hours_night'));
    }

    public function dayHoursOnTravel(string $date): float
    {
        return Time::where('date', $date)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum('hours_travel');
    }

    /**
     * Get the week billable day hours for the user.
     * @param string $start date format 'Y-m-d'
     * @param string $end date format 'Y-m-d'
     * @return float
     */
    public function weekProductiveHoursOnDay(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum('hours_day');
    }

    /**
     * [SPECGT25] - Retrait des heures d'astreinte du calcul des heures non productives
     * Get the week non-billable day hours for the user.
     * @param string $start date format 'Y-m-d'
     * @param string $end date format 'Y-m-d'
     * @return float
     */
    public function weekNonProductiveHoursOnDay(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNull('chantier_id')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum('hours_day');
    }

    /**
     * Get the week billable night hours for the user.
     * @param string $start date format 'Y-m-d'
     * @param string $end date format 'Y-m-d'
     * @return float
     */
    public function weekProductiveHoursOnNight(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum('hours_night');
    }

    /**
     * [SPECGT25] - Retrait des heures d'astreinte du calcul des heures non productives
     * Get the week non-billable night hours for the user.
     * @param string $start date format 'Y-m-d'
     * @param string $end date format 'Y-m-d'
     * @return float
     */
    public function weekNonProductiveHoursOnNight(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNull('chantier_id')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum('hours_night');
    }

    /**
     * Get the week billable hours for the user.
     * @param string $start date format 'Y-m-d'
     * @param string $end date format 'Y-m-d'
     * @return float
     */
    public function weekProductive(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum(DB::raw('hours_day + hours_night'));
    }

    /**
     * [SPECGT25] - Retrait des heures d'astreinte du calcul des heures non productives
     * Get the week non-billable hours for the user.
     * @param string $start date format 'Y-m-d'
     * @param string $end date format 'Y-m-d'
     * @return float
     */
    public function weekNonProductive(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNull('chantier_id')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum(DB::raw('hours_day + hours_night'));
    }

    public function weekHoursOnTravel(string $start, string $end): float
    {
        return Time::whereBetween('date', [$start, $end])
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->whereNotNull('chantier_id')
            ->sum('hours_travel');
    }

        /**
     * [SPECMBA03]
     * Get the day where the user has worked travel hours.
     * @param string $date date format 'Y-m-d'
     * @return array
     */
    public function monthlyTravelEventsDays(string $date): array
    {
        $date = Carbon::parse($date);
        $year = $date->year;
        $month = $date->month;

        return Time::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('user_id', $this->user->id)
            ->where('hours_travel', '>', 0)
            ->whereNotNull('chantier_id')
            ->get()
            ->toArray();
    }

    /**
     *  [SPECGT25] - Retrait des heures d'astreinte du calcul des heures non productives
     *  Get the month hours for the user.
     * @param string $date
     * @return float
     */
    public function monthlyDayHours(string $date): float
    {
        $date = Carbon::parse($date);
        $year = $date->year;
        $month = $date->month;

        return Time::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->sum(DB::raw('hours_day'));
    }

    public function monthlyNightHours(string $date): float
    {
        $date = Carbon::parse($date);
        $year = $date->year;
        $month = $date->month;

        return Time::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->sum(DB::raw('hours_night'));
    }

    public function monthlyTravelsHours(string $date): float
    {
        $date = Carbon::parse($date);
        $year = $date->year;
        $month = $date->month;

        return Time::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('user_id', $this->user->id)
            ->whereNull('state')
            ->sum(DB::raw('hours_travel'));
    }
    // FIN [SPECGT26] - Création de la classe de service pour le calcul des heures



    /**
     * [SPECMBA04] - Calculer la moyenne du taux horaire sur toutes les données
     * Get the hourly rate from all the datas.
     * @return float
     */
    private function getMedianHourlyRate(): float
    {
        $medianMOE = Chantier::sum('serviceamount');
        $medianTime = Time::whereNotNull('chantier_id')
            ->select(DB::raw('SUM(hours_day + hours_night + hours_travel) AS total_hours'))
            ->value('total_hours');
        $unproductiveHours = Time::whereNull('state')
            ->whereNull('chantier_id')
            ->where('on_callduty', 0)
            ->where('on_business_trip', 0)
            ->sum('hours_day');

        if ($medianMOE - $unproductiveHours < 0) return 0;
        return $medianTime / ($medianMOE - $unproductiveHours);
    } 
}
