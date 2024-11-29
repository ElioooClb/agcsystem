<?php

namespace App\Http\Controllers;

use App\Models\Loadout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkSiteStatesService;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Chantier;
use App\Models\Time;
use App\Services\EmailService;
use Exception;

class ChantierController extends Controller
{
    // Bonne pratique : injection de dépendance, requête sql située dans le service.
    private $colorsDefinitions = [
        'green' => 'ROP',
        'yellow' => 'Electronic System',
        'red' => 'Maintenance',
        'purple' => 'LAN',
        'blue' => 'RACCO',
        'gray' => 'FON',
        'orange' => 'Network Life'
    ];

    public function __construct(private WorkSiteStatesService $workSiteStatesService, private EmailService $emailService) {}

    public function create()
    {
        $users = User::all();
        $loadouts = Loadout::with('parameters')->get(); // Inclure les paramètres
        return view(
            'chantier.create',
            ['users' => $users, 'loadouts' => $loadouts]
        );
    }
    
    /**
     * Store a newly created resource in storage.
     * [SPECGT9] - Définition de l'état initial du chantier fais dans la base de donnée par défaut
     * [SPECGT7] -  obligation de saisir le titre/ montant materiel et montant service lors de la création d'une carte chantier
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @version 1 - 2021-09-07 [SPECGT9]
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'materialamount' => ['required', 'numeric', 'min:0'], // Ajout de la validation du montant matériel supérieux à 1, commentaire à supprimer
            'serviceamount' => ['required', 'numeric', 'min:1'], // Ajout de la validation du montant service supérieux à 1, commentaire à supprimer
            'hours' => ['required', 'numeric', 'min:1'], // Ajout de la validation des heures supérieures à 1, commentaire à supprimer
        ]);
        $chantier = new Chantier;
        $chantier->title = $request->input('title');
        $chantier->hours = $request->input('hours'); // Valeur par défaut à null en bd si la valeur est nulle pas besoin de condition - Commentaire à supprimer
        $chantier->materialamount = $request->input('materialamount');
        $chantier->serviceamount = $request->input('serviceamount');
        // Début [SPECGT6] - Ajout des loadouts aux chantiers et suppression des anciens paramètres
        $chantier->loadout_id = $request->input('loadout_id');
        // Fin [SPECGT6] - Ajout des loadouts aux chantiers et suppression des anciens paramètres
        $chantier->color = $request->input('color');
        // DEBUT - [SPECGT3] Ajout du superviseur de chantier
        $chantier->supervisor_id = Auth()->user()->id;
        // FIN - [SPECGT3] Ajout du superviseur de chantier
        $chantier->save();
        // FIN - [SPECGT9] - Définition de l'état initial du chantier fais dans la base de donnée par défaut
        $chantierId = $chantier->id;

        $loadout = Loadout::find($request->input('loadout_id'));
        foreach ($loadout->parameters as $parameter) {
            $chantier->parameters()->attach($parameter, ['completed' => false]);
        }

        $assignedUsers = json_decode($request->input('assigned_users'));
        if ($assignedUsers != null) {
            foreach ($assignedUsers as $user) {
                $user = User::find($user);
                $user->chantiers()->attach($chantierId);
            }
        }

        // DEBUT [SPECGT3] - Envoi d'un email de notification lors de la création d'un chantier
        $config = [
            'to' => 'admin',
            'subject' => 'Nouveau chantier créé - ' . $chantier->getIdAff(),
            'template' => 'emails.worksite.notification',
        ];
        $emailData = [
            'title' => $chantier->title,
            'idaff' => $chantier->getIdAff(),
            'supervisor' => $chantier->supervisor->name,
            'created_at' => $chantier->created_at,
            'hours' => $chantier->hours ?? 'Non défini',
            'type' => $this->colorsDefinitions[$chantier->color],
            'materialamount' => $chantier->materialamount,
            'serviceamount' => $chantier->serviceamount,

        ];
        $this->emailService->sendEmailNotification($config, $emailData);
        // FIN [SPECGT3] - Envoi d'un email de notification lors de la création d'un chantier
        return redirect()->route('planning.index', Auth::user())->withStatus('Le chantier a bien été créé !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idChantier)
    {
        $chantier = Chantier::find($idChantier);

        if ($chantier) {
            try {
                $chantier->delete();
                return response()->json(['success' => 'Chantier supprimé avec succès !'], 200);
            } catch (Exception $e) {
                return response()->json(['error' => 'Impossible de supprimer le chantier, il est lié à d\'autres données.'], 400);
            }
        }
        return response()->json(['error' => 'Chantier non trouvé !'], 404);
    }

    /**
     * Remove the specified resource from storage with dependancies.
     * [SPECMBA°1] - 02/10/2024 - Updated the deletion of work sites with dependancies
     */
    public function forceDestroy($idChantier)
    {
        $chantier = Chantier::find($idChantier);
        $workers = $chantier->users;
        foreach ($workers as $worker) {
            $worker->chantiers()->detach($idChantier);
        }
        $chantier->parameters()->detach();
        $chantier->supervisor()->dissociate();
        $chantier->loadout()->dissociate();
        $chantier->events()->delete();
        $chantier->times()->delete();
        $chantier->delete();
        return response()->json(['success' => 'Chantier supprimé avec succès !'], 200);
    }

    /**
     * Page Admin display filter for work sites
     * Display a listing of the work sites.
     * [SPECMBA01] - 02/10/2024 - Updated the display of work sites with the work sites number to the french format
     */
    public function index()
    {
        // DEBUT - [SPECGT26] - Optimisation des requêtes avec dépendances
        $chantiers = Chantier::with('times')->get();
        /** @var \App\Models\Chantier */
        foreach ($chantiers as $chantier) {
            $chantier->setAttribute("totalHours", $chantier->times->sum("hours_day") + $chantier->times->sum("hours_night"));
            $chantier->realisation_date = $chantier->realisation_date ? Carbon::parse($chantier->realisation_date)->format('d/m/Y') : '';
            $chantier->setAttribute("formattedEstimatedTotalHours", number_format($chantier->totalHours, 2, ',', ' '));
            $chantier->setAttribute("formattedEstimatedMaterialAmount", number_format($chantier->materialamount, 2, ',', ' '));
            $chantier->setAttribute("formattedEstimatedServicesAmount", number_format($chantier->serviceamount, 2, ',', ' '));
        }
        // FIN - [SPECGT26] - Optimisation des requêtes
        return view('chantier.index', [
            'chantiers' => $chantiers,
        ]);
    }

    public function planning(User $user)
    {
        $user = User::find($user->id);
        $chantierIds = $user->chantiers->pluck('id')->toArray();
        return view('users.suivi.index', [
            'user' => $user,
            'chantiers' => $chantierIds,
        ]);
    }

    public function updateTitle($idChantier)
    {
        $chantier = Chantier::find($idChantier);
        $chantier->title = request()->input('title');
        $events = $chantier->events;
        foreach ($events as $event) {
            $event->title = $chantier->title;
            $event->save();
        }
        $chantier->save();
    }

    public function updateDevis($idChantier)
    {
        $chantier = Chantier::find($idChantier);
        $chantier->hours = request()->input('hour');
        $chantier->save();
    }

    public function assignUser($idChantier, $idUser)
    {
        $chantier = Chantier::find($idChantier);
        $user = User::find($idUser);
        if (!$chantier->users->contains($user)) {
            $chantier->users()->attach($user);
        }
    }

    public function optionChantier($option, $idChantier)
    {
        $chantier = Chantier::find($idChantier);
        if ($option == 'color') {
            $chantier->color = request()->input('color');
        } else {
            $chantier->$option = request()->has($option);
        }
        $chantier->save();
    }

    public function deleteUserChantier($idChantier, $idUser)
    {
        $chantier = Chantier::find($idChantier);
        $user = User::find($idUser);
        $chantier->users()->detach($user);
    }

    public function addObservations($idChantier)
    {
        $chantier = Chantier::find($idChantier);
        $observation = request()->input('observation');
        $chantier->observation = $observation;
        $chantier->save();
    }

    public function updateMontant($idChantier)
    {
        $chantier = Chantier::find($idChantier);
        $amounts = request()->query('amounts');
        $amountsArray = explode(',', $amounts);
        if ($amountsArray[0] == '') {
            $amountsArray[0] = 0;
        }
        $chantier->materialamount = $amountsArray[0];

        if ($amountsArray[1] == '') {
            $amountsArray[1] = 0;
        }
        $chantier->materialamount = $amountsArray[0];

        $chantier->serviceamount = $amountsArray[1];
        $chantier->save();
    }

    public function updateColor($idChantier, $color)
    {
        $chantier = Chantier::find($idChantier);
        $chantier->color = $color;
        $chantier->save();
    }

    public function show(Chantier $chantier)
    {
        $userChantier = $chantier->users()->pluck('user_id');
        $chantiersUsers = User::whereIn('id', $userChantier)->get();
        // Jointure de la table `times` avec la table `users`
        $time = Time::join('users', 'users.id', '=', 'times.user_id')
            ->where('times.chantier_id', $chantier->id)
            ->select('times.*', 'users.name')
            ->get();
        $chantiersHours = Time::where('chantier_id', $chantier->id)->get();
        $totalHours = $chantiersHours->sum('hours_day') + $chantiersHours->sum('hours_night');
        $hours = floor($totalHours);
        $minutes = round(($totalHours - $hours) * 60);
        if ($minutes == 0) {
            $minutes = '00';
        }
        $totalTime = $hours . 'h' . $minutes;
        $chantier->totalTime = $totalTime;
        $chantier->realisation_date = Carbon::parse($chantier->realisation_date)->format('d/m/Y');

        return view('chantier.show', [
            'chantier' => $chantier,
            'chantiersUsers' => $chantiersUsers,
            'time' => $time,
        ]);
    }

    public function findChantier($idChantier)
    {
        $chantier = Chantier::with('users')->find($idChantier);
        return $chantier;
    }

    public function findChantierHours($idChantier)
    {
        $chantier = Chantier::with(['users', 'times'])->find($idChantier);
        return $chantier;
    }

    public function updateRealisationDate($idChantier)
    {
        $chantier = Chantier::find($idChantier);
        $currentDate = Carbon::now();
        $chantier->realisation_date = $currentDate;
        $chantier->save();
    }

    /**
     * Handle the state (update|downgrade) of a work site
     * [SPECGT9] -> [SPECGT21] -> [SPECGT20] -> [SPECGT3]
     * @param Request $request - Request object {id, state, action, return = null}
     * @return JsonResponse
     */
    public function handleState(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id' => 'required|numeric',
            'state' => 'required|string',
            'action' => 'required|string',
            'return' => 'sometimes|boolean',
        ]);

        $workSiteId = $validatedData['id'];
        $state = $validatedData['state'];
        $action = $validatedData['action'];
        $return = $validatedData['return'] ?? false;

        // DEBUT [SPECGT21] - Ajout de la gestion des numéros de facture
        switch ($action) {
            case 'update':
                $workSite = Chantier::with('supervisor')->with('invoices')->find($workSiteId);
                $workSiteEmailData = [
                    'title' => $workSite->title,
                    'idaff' => $workSite->getIdAff(),
                    'invoiceNumber' => $workSite->invoices && $workSite->invoices->number ? $workSite->invoices->number : null,
                    'supervisor' => $workSite->supervisor->name,
                    'created_at' => $workSite->created_at,
                ];
                // DEBUT [SPECGT3] - Envoi d'un email de notification lors de la modification de l'état d'un chantier
                switch ($state) {
                    case 'billable':
                    case 'partiallyBilled':
                        $state === 'billable' ? $workSiteEmailData['isFinal'] = true : $workSiteEmailData['isFinal'] = false;
                        $workSiteEmailData['isAccountant'] = true;
                        $config = [
                            'to' => 'accountant',
                            'subject' => 'Demande de facturation - ' . $workSiteEmailData['idaff'],
                            'template' => 'emails.invoice.notification',
                        ];
                        $this->emailService->sendEmailNotification($config, $workSiteEmailData);
                        break;
                    case 'pendingApproval':
                    case 'pendingArchiving':
                        $state === 'pendingArchiving' ? $workSiteEmailData['isFinal'] = true : $workSiteEmailData['isFinal'] = false;
                        $workSiteEmailData['isAccountant'] = false;
                        $supervisorMail = $workSite->supervisor->email;
                        if (!$supervisorMail) return response()->json(['errors' => 'Supervisor email not found'], 400);
                        $config = [
                            'to' => 'supervisor',
                            'subject' => 'Facturation validée - ' . $workSiteEmailData['idaff'],
                            'supervisorEmail' => $supervisorMail,
                            'template' => 'emails.invoice.notification',
                        ];
                        $this->emailService->sendEmailNotification($config, $workSiteEmailData);

                        // Delete the loadout_id when the work site is pending archiving
                        $workSite->loadout_id = null;
                        $workSite->parameters()->detach();
                        $workSite->save();
                        break;
                }
                // FIN [SPECGT3] - Envoi d'un email de notification lors de la modification de l'état d'un chantier
                $response = $this->handleStateChange($workSiteId, $state, true);
                break;
            case 'downgrade':
                $response = $this->handleStateChange($workSiteId, $state, false);
                break;
            default:
                $response = response()->json(['errors' => 'Action not found'], 400);
                break;
        }
        if ($response->getData() !== null && property_exists($response->getData(), 'errors')) return $response;
        if ($return) {
            $workSite = Chantier::where('id', $workSiteId)->with('states')->with('invoices')->first();
            $responseData = $response->getData(true);
            $responseData['additionalData'] = $workSite;
            $response->setData($responseData);
        }
        return $response;
        // FIN [SPECGT21] - Ajout de la gestion des numéros de facture
    }

    /**
     * Handle the state change of a work site
     * [SPECGT9]
     * @param int $idChantier - Work site id
     * @param string $state - Work site state
     * @param bool $isUpdate - True if the state change is an update, false otherwise
     * @return JsonResponse
     */
    private function handleStateChange(int $id, string $state, bool $isUpdate)
    {
        $this->workSiteStatesService->initWorkSite($id);
        return $isUpdate ? $this->workSiteStatesService->updateState($state) : $this->workSiteStatesService->downgradeState($state);
    }

    /**
     * Update a work site parameter
     * [SPECGT6]
     * @param int $worksiteId - Work site id
     * @param int $parameterId - Parameter id
     * @param string $checked - Checked value
     */
    public function updateParameter($worksiteId, $parameterId, $checked): void
    {
        $worksite = Chantier::find($worksiteId);
        $parameter = $worksite->parameters()->where('parameter_id', $parameterId)->first();
        $parameter->pivot->completed = $checked === 'true' ? 1 : 0;
        $parameter->pivot->save();
    }

    /**
     * Display the work site statistics
     * @version 1 - 2021-09-07 [SPECMBA06]
     * @return \Illuminate\Contracts\View\View
     */
    public function statistiques() 
    {
        return view('chantier.statistics');
    }
}
