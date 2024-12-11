<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\User;
use App\Services\CalculOnHoursService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TimeController extends Controller
{

    public function __construct(private CalculOnHoursService $calculOnHoursService) {}

    /**
     * Début [SPECGT28], [SPECGT24], [SPECGT25] - Modification des noms des inputs + renommage dans la suite du code + ajout heures trajet + ajout heures astreinte
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        // Retrieve the input values from the request
        $date = $request->input('date');
        $dayHours = $request->input('dHours');
        $nightHours = $request->input('nHours');
        $passengerHours = $request->input('pHours');
        $userId = $request->input('userId');
        $worksiteId = $request->input('worksiteId');
        $note = $request->input('note');
        $type = $request->input('type');
        // dd($type);
        switch ($type) {
            case '1': // Astreinte
                Log::info('Option sélectionnée : Astreinte');
                $currentDate = Carbon::parse($date);
                $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);  // Récupère le lundi de la semaine en cours

                $weekDays = [];
                for ($i = 0; $i < 7; $i++) {
                    $weekDays[] = $startOfWeek->copy()->addDays($i)->toDateString();
                }

                foreach ($weekDays as $day) {
                    // Logique pour créer des événements pour chaque jour de la semaine
                    $entry = Time::where('date', $day)->where('user_id', $userId)->first();
                    if ($entry) {
                        // Mettre à jour l'événement
                        $entry->update([
                            'hours_day' => 7.00,
                            'oncall_duty' => 1,
                            'note' => $note,
                        ]);
                    } else {
                        // Créer un nouvel événement
                        $time = new Time;
                        $time->user_id = $userId;
                        $time->date = $day;
                        $time->hours_day = (int)explode(':', $dayHours)[0] + (int)explode(':', $dayHours)[1] / 60;
                        $time->hours_night = 0.00;
                        $time->oncall_duty = 1;
                        $time->note = $note;
                        $time->save();
                    }
                }
                break;

            case '2': // Grand trajet
                $entry = Time::where('date', $date)
                    ->where('user_id', $userId)
                    ->where('on_business_trip', 1)
                    ->first();
                if ($entry) {
                    $entry->update([
                        'hours_day' => 0.00,
                        'on_business_trip' => 1,
                        'note' => $note,
                    ]);
                    return redirect()->route('time.shows', $userId)->withStatus('Le grand tajet a bien été mise à jour !');
                } else {
                    $time = new Time;
                    $time->user_id = $userId;
                    $time->date = $date;
                    $time->hours_day = (int)explode(':', $dayHours)[0] + (int)explode(':', $dayHours)[1] / 60;
                    $time->hours_night = 0.00;
                    $time->on_business_trip = 1;
                    $time->note = $note;
                    $time->save();
                }
                break;

            case '3': // Intervention non facturée
                $entry = Time::where('date', $date)
                    ->where('user_id', $userId)
                    ->where('unbillable', 1)
                    ->first();
                if ($entry) {
                    $entry->update([
                        'hours_day' => (int)explode(':', $dayHours)[0] + (int)explode(':', $dayHours)[1] / 60,
                        'hours_night' => (int)explode(':', $nightHours)[0] + (int)explode(':', $nightHours)[1] / 60,
                        'hours_travel' => (int)explode(':', $passengerHours)[0] + (int)explode(':', $passengerHours)[1] / 60,
                        'unbillable' => 1,
                        'note' => $note,
                    ]);
                    return redirect()->route('time.shows', $userId)->withStatus('L\'heure non facturée a bien été mise à jour !');
                } else {
                    $time = new Time;
                    $time->user_id = $userId;
                    $time->date = $date;
                    $time->hours_day = (int)explode(':', $dayHours)[0] + (int)explode(':', $dayHours)[1] / 60;
                    $time->hours_night = 0.00;
                    $time->unbillable = 1;
                    $time->note = $note;
                    $time->save();
                }
                break;

            default: // Heure hors production
                $entry = Time::where('date', $date)
                    ->where('user_id', $userId)
                    ->where('chantier_id', $worksiteId)
                    ->whereNull('state')
                    ->where('oncall_duty', 0)
                    ->where('unbillable', 0)
                    ->first();
                if ($entry) {
                    $entry->update([
                        'hours_day' => (int)explode(':', $dayHours)[0] + (int)explode(':', $dayHours)[1] / 60,
                        'note' => $note,
                    ]);
                    return redirect()->route('time.shows', $userId)->withStatus('L\'heure hors production a bien été mise à jour !');
                } else {
                    $time = new Time;
                    $time->user_id = $userId;
                    $time->date = $date;
                    $time->hours_day = (int)explode(':', $dayHours)[0] + (int)explode(':', $dayHours)[1] / 60;
                    $time->hours_night = 0.00;
                    $time->chantier_id = $worksiteId;
                    $time->note = $note;
                    $time->save();
                }
                break;
        }
        // // Redirect to the 'time.shows' route with a success message
        return redirect()->route('time.shows', $userId)->withStatus('Le chantier a bien été créé !');
    }

    /**
     * Affiche le planning d'un utilisateur pour mettre à jour ses heures avec ses chantiers
     * [SPECGT28] - Modification de la méthode showHeure pour afficher les totaux d'heures par jour
     * @param User $user
     * @return \Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function showHeure(User $user)
    {
        $user = User::find($user->id);
        $times = Time::where('user_id', $user->id)->get();

        $this->calculOnHoursService->setUser($user);

        $productiveHours = [];
        $nonProductiveHours = [];
        foreach ($times as $time) {
            // Si l'heure a un état, ne pas inclure les heures "normales" pour le même jour
            if ($time->state) {
                $times = $times->filter(function ($item) use ($time) {
                    return $item->date != $time->date || $item->state;
                });
            }

            $productiveHours[$time->date] = $this->calculOnHoursService->dayProductiveHours($time->date);
            $nonProductiveHours[$time->date] = $this->calculOnHoursService->dayNonProductiveHours($time->date);
        }

        // Variable pour stocker les ids des chantiers de l'utilisateur
        $chantiers = $user->chantiers;


        return view('users.suivi.showHeure', [
            'user' => $user,
            'times' => $times,
            'chantiers' => $chantiers,
            'productiveHours' => $productiveHours,
            'nonProductiveHours' => $nonProductiveHours
        ]);
    }

    // Voir le planning d'un utilsateur pour mettre à jour ses heures avec ces chantiers
    public function tekosTimeAll()
    {
        $users = User::all();
        return view('tekos.allTime', compact('users'));
    }

    // Voir le planning d'un utilsateur avec ses chantiers et ses heures
    public function tekosTimeShow(User $user)
    {
        $chantierIds = $user->chantiers->pluck('id')->toArray();
        $times = Time::where('user_id', $user->id)->get();
        return view('tekos.show', [
            'user' => $user,
            'chantiers' => $chantierIds,
            'times' => $times,
        ]);
    }

    public function absenceUser($userId, $date, $hours, $value)
    {

        if (Time::where('date', $date)->where('user_id', $userId)->whereNotNull('state')->exists()) {
            $time = Time::where('user_id', $userId)->where('date', $date)->whereNotNull('state')->where('on_business_trip', 0)->first();
            $time->state = $value;
            $time->hours_day = (int)explode(':', $hours)[0] + (int)explode(':', $hours)[1] / 60;
            $time->save();
        } else {
            // Stocker les données dans la base de données
            if ($hours == "00:00")
                $hours = "07:00";
            $time = new Time;
            $time->user_id = $userId;
            $time->date = $date;
            $time->hours_day = (int)explode(':', $hours)[0] + (int)explode(':', $hours)[1] / 60;
            $time->state = $value;
            $time->save();
        }
    }

    public function deleteAbscence($userId, $date)
    {
        if (Time::where('date', $date)->where('user_id', $userId)->whereNotNull('state')->where('on_business_trip', 0)->exists()) {
            $time = Time::where('user_id', $userId)->where('date', $date)->whereNotNull('state')->first();
            $time->delete();
        }
    }

    /**
     * Delete the time of a user for a specific date and chantier
     * [SPECGT28] - Modification de la méthode deleteTimeChantier et deleteTime
     * @param number $userId
     * @param date $date
     * @param number $chantierId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteTimeChantier($userId, $date, $chantierId)
    {
        if (Time::where('date', $date)
            ->where('user_id', $userId)
            ->where('chantier_id', $chantierId)
            ->whereNull('state')
            ->where('on_business_trip', 0)
            ->exists()
        ) {
            $time = Time::where('user_id', $userId)
                ->where('date', $date)
                ->where('chantier_id', $chantierId)
                ->first();
            $time->delete();

            // Return a valid JSON response
            return response()->json(['success' => true]);
        }

        // If the time does not exist, return an error message
        return response()->json(['error' => 'Time not found'], 404);
    }

    /**
     * Delete the time of a user for a specific date
     * [SPECGT28] - Modification de la méthode deleteTimeChantier et deleteTime
     * @param number $userId
     * @param date $date
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteTime($userId, $date)
    {
        if (Time::where('date', $date)
            ->where('user_id', $userId)
            ->whereNull('state')
            ->whereNull('chantier_id')
            ->where('oncall_duty', 0)
            ->where('on_business_trip', 0)
            ->exists()
        ) {
            $time = Time::where('user_id', $userId)
                ->where('date', $date)
                ->whereNull('state')
                ->whereNull('chantier_id')
                ->where('oncall_duty', 0)
                ->where('on_business_trip', 0)
                ->first();
            $time->delete();

            // Return a valid JSON response
            return response()->json(['success' => true]);
        }

        // If the time does not exist, return an error message
        return response()->json(['error' => 'Time not found'], 404);
    }

    /**
     * Delete the time of a user for a specific date
     * [SPECGT25] - Méthode de suppression des heures d'astreinte
     * @param number $userId
     * @param date $date
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteOncallDutyTime($userId, $date)
    {
        if (Time::where('date', $date)
            ->where('user_id', $userId)
            ->whereNull('state')
            ->whereNull('chantier_id')
            ->where('oncall_duty', 1)
            ->exists()
        ) {
            $time = Time::where('user_id', $userId)
                ->where('date', $date)
                ->whereNull('state')
                ->whereNull('chantier_id')
                ->where('oncall_duty', 1)
                ->first();
            $time->delete();

            // Return a valid JSON response
            return response()->json(['success' => true]);
        }

        // If the time does not exist, return an error message
        return response()->json(['error' => 'Time not found'], 404);
    }

    public function findTime($timeId)
    {
        $time = Time::find($timeId);
        return $time;
    }


    // Voir le planning d'un utilsateur supprime
    public function tekosTimeDist()
    {
        $users = User::all();
        return view('tekos.allDist', compact('users'));
    }

    /**
     * [SPECGT25] - Méthode pour ajouter une heure d'astreinte
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOncallDutyTime(Request $request)
    {
        // Retrieve the input values from the request
        $date = $request->input('date');
        $userId = $request->input('userId');

        // Check if an oncall duty Time entry already exists for the given date and user
        if (Time::where('date', $date)
            ->where('user_id', $userId)
            ->where('oncall_duty', 1)
            ->exists()
        ) {
            // If an entry exists, return a response indicating that an oncall duty entry already exists for this day
            return response()->json(['success' => false, 'message' => 'Une entrée d\'heure d\'astreinte existe déjà pour ce jour.']);
        }

        // If no entry exists, create a new oncall duty Time entry
        $time = new Time;
        $time->date = $date;
        $time->user_id = $userId;
        $time->hours_day = 7; // 7 hours for oncall duty
        $time->hours_night = 0;
        $time->oncall_duty = 1; // oncall duty is true
        $time->save();

        return response()->json(['success' => true]);
    }

    /**
     * Add or create a new time for a user specifying if he is on a business trip
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBusinessTripTime(Request $request)
    {
        // Retrieve the input values from the request
        $date = $request->input('date');
        $userId = $request->input('userId');

        // Check if an oncall duty Time entry already exists for the given date and user
        if (Time::where('date', $date)
            ->where('user_id', $userId)
            ->where('on_business_trip', 1)
            ->exists()
        ) {
            // If an entry exists, return a response indicating that an oncall duty entry already exists for this day
            return response()->json(['success' => false, 'message' => 'Une entrée de grand tajet existe déjà pour ce jour.']);
        }

        // If no entry exists, create a new oncall duty Time entry
        $time = new Time;
        $time->date = $date;
        $time->user_id = $userId;
        $time->hours_day = 0;
        $time->hours_night = 0;
        $time->hours_travel = 0;
        $time->on_business_trip = 1; // on_business_trip is true
        $time->save();

        return response()->json(['success' => true]);
    }

    /**
     * Delete the business trip data of a user
     * @param number $userId
     * @param date $date
     */
    public function deleteBusinessTripTime($userId, $date)
    {
        $user = Auth::user();
        $entry = Time::where('date', $date)
            ->where('user_id', $userId)
            ->where('on_business_trip', 1)
            ->first();

        if ($entry) {
            $entry->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'Time not found'], 404);
    }

    /**
     * Add unbilled intervention time
     * [SPECMBA08] - Ajout d'une intervention non facturée
     * @param Request $request
     */
    public function addUnbilledInterventionTime(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date');
        $hours = $request->input('hours');

        $entry = Time::where('date', $date)
            ->where('user_id', $user->id)
            ->where('unbillable', 1)
            ->first();

        if ($entry) {
            $entry->update([
                'hours_day' => $this->convertTimeToDecimal($hours),
            ]);
        } else {
            $time = new Time;
            $time->user_id = $user->id;
            $time->date = $date;
            $time->hours_day = $this->convertTimeToDecimal($hours);
            $time->hours_night = 0;
            $time->hours_travel = 0;
            $time->unbillable = 1;
            $time->save();
        }
    }

    private function convertTimeToDecimal($time)
    {
        // Séparer l'heure et les minutes
        list($hours, $minutes) = explode(':', $time);

        // Convertir les minutes en fraction d'heure
        $decimal = $hours + ($minutes / 60);

        // Retourner la valeur en double avec 2 décimales
        return number_format($decimal, 2, '.', '');
    }

    /**
     * Delete unbilled intervention time
     * [SPECMBA08] - Suppression d'une intervention non facturée
     */
    public function deleteUnbilledInterventionTime($date)
    {
        $user = Auth::user();
        $entry = Time::where('date', $date)
            ->where('user_id', $user->id)
            ->where('unbillable', 1)
            ->first();

        if ($entry) {
            $entry->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'Time not found'], 404);
    }
}
