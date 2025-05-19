<?php

use App\Http\Livewire\{
    HeureTekosAdmin
};

use App\Http\Controllers\{
    ProfileController,
    MessageController,
    UserController,
    TimeController,
    ChantierController,
    InvoiceController,
    EmailTemplateController,
    LoadoutController,
    ManageController,
    ParameterController,
    StateController,
    CustomEventController,
    AvatarController,
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**page de connexion */
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /** Planning admnistrateur */
    Route::get('/Planning/{user}', [ChantierController::class, 'planning'])->name('planning.index');

    /** Gestion des utilisateurs administrateur */
    Route::get('/gestion-des-utilisateurs', [UserController::class, 'index'])->name('users.index');
    Route::get('/nouvel-utilisateur', [UserController::class, 'create'])->name('user.create');
    Route::post('/nouvel-utilisateur', [UserController::class, 'store'])->name('user.store');
    Route::get('Destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('Editer-l-utilisateur/{user}', [UserController::class, 'edite'])->name('user.edite');
    Route::put('Editer-l-utilisateur/{user}', [UserController::class, 'update'])->name('user.update');
    Route::put('Editer-le-mot-de-passe/{user}', [UserController::class, 'updatePassword'])->name('user.updatePassword');

    /** Gestion des chantier pour l'affichage du planning */
    Route::get('/creation-chantier-planning', [ChantierController::class, 'create'])->name('chantier.create');
    Route::post('/creation-chantier-planning', [ChantierController::class, 'store'])->name('chantier.store');
    Route::delete('/supprimer-chantier-planning/{idChantier}', [ChantierController::class, 'destroy'])->name('chantier.destroy');
    Route::delete('/supprimer-chantier-planning/force/{idChantier}', [ChantierController::class, 'forceDestroy'])->name('chantier.destroyWithDependancies');
    Route::get('/modifier-titre-chantier/{idChantier}', [ChantierController::class, 'updateTitle'])->name('chantier.updateTitle');
    Route::put('/modifier-devis-chantier/{idChantier}', [ChantierController::class, 'updateDevis'])->name('chantier.updateDevis');
    Route::put('/ajouter-user-chantier/{idChantier}/{idUser}', [ChantierController::class, 'assignUser'])->name('chantier.assignUser');
    Route::delete('/supprimer-user-chantier/{idChantier}/{idUser}', [ChantierController::class, 'deleteUserChantier'])->name('chantier.deleteUserChantier');
    Route::put('/ajouter-observation-chantier/{idChantier}', [ChantierController::class, 'addObservations'])->name('chantier.addObservations');

    /** Gestion des états */
    Route::put('/handle-state', [ChantierController::class, 'handleState']);
    Route::put('/handle-invoice', [InvoiceController::class, 'handleInvoice']);
    Route::delete('/delete-invoice', [InvoiceController::class, 'delete']);
    Route::post('/recovery-invoice', [InvoiceController::class, 'recovery']);

    Route::post('/handle-stage', [StateController::class, 'handleStage']);

    /** Gestion des statistiques */
    Route::get('/voir-les-statistiques', [ChantierController::class, 'statistiques'])->name('chantier.statistiques');

    /** Gestion des chantier admin */
    Route::get('/Gestion-chantier', [ChantierController::class, 'index'])->name('chantier.index');
    Route::put('/modifier-couleur/{idChantier}/{color}', [ChantierController::class, 'updateColor'])->name('chantier.updateColor');
    Route::get('/modifier-montant/{idChantier}', [ChantierController::class, 'updateMontant'])->name('chantier.updateMontant');
    Route::put('/modifier-date-realisation/{idChantier}', [ChantierController::class, 'updateRealisationDate'])->name('chantier.updateRealisationDate');
    Route::get('/voir-chantier/{chantier}', [ChantierController::class, 'show'])->name('chantier.show');
    Route::get('/trouver-chantier/{idChantier}', [ChantierController::class, 'findChantier'])->name('chantier.findChantier');
    Route::get('/trouver-chantier/heures/{idChantier}', [ChantierController::class, 'findChantierHours'])->name('chantier.findChantierHours');
    Route::get('/rechercher/{idChantier}', [ChantierController::class, 'findChantierById']);

    /** gestion des heure chantier tekos */
    Route::post('/nouvelle-heure', [TimeController::class, 'store'])->name('time.store');
    Route::get('/Voir-mes-heures/{user}', [TimeController::class, 'showHeure'])->name('time.shows');

    /** heures des tekos admin */
    Route::get('/Voir-les-heures-des-tekos', [TimeController::class, 'tekosTimeAll'])->name('tekosTime.index');
    Route::get('/Voir-les-heures/{user}', [TimeController::class, 'tekosTimeShow'])->name('tekosTime.show');
    Route::get('/trouver-declaration/{timeId}', [TimeController::class, 'findTime'])->name('time.findTime');
    Route::get('/absences/{userId}/{date}/{hours}/{value}', [TimeController::class, 'absenceUser'])->name('time.absenceUser');
    Route::get('/supprimer-absences/{userId}/{date}', [TimeController::class, 'deleteAbscence'])->name('time.deleteAbscence');
    Route::delete('/supprimer-heure-chantier/{userId}/{date}/{chantierId}', [TimeController::class, 'deleteTimeChantier'])->name('time.deleteTimeChantier');
    Route::delete('/supprimer-heure/{userId}/{date}', [TimeController::class, 'deleteTime'])->name('time.deleteTime');
    Route::get('/Voir-les-heures-des-tekos-supprimes', [TimeController::class, 'tekosTimeDist'])->name('tekosTime.dist');

    /** message admin sur affichage demo */
    Route::get('/Voir-les-messages', [MessageController::class, 'index'])->name('message.index');
    Route::get('/Nouveau-message', [MessageController::class, 'create'])->name('message.create');
    Route::post('/Nouveau-message', [MessageController::class, 'store'])->name('message.store');
    Route::get('Editer-un-message/{message}', [MessageController::class, 'edit'])->name('message.edit');
    Route::put('Editer-un-message/{message}', [MessageController::class, 'update'])->name('message.update');
    Route::post('Publication-slider', [MessageController::class, 'publier']);
    Route::get('Destroy-the-message/{id}', [MessageController::class, 'destroy'])->name('message.destroy');

    Route::post('/ajouter-heure-astreinte', [TimeController::class, 'addOncallDutyTime']);
    Route::delete('/supprimer-heure-astreinte/{userId}/{date}', [TimeController::class, 'deleteOncallDutyTime']);
    Route::post('/ajouter-heure-business-trip', [TimeController::class, 'addBusinessTripTime']);
    Route::delete('/supprimer-heure-business-trip/{userId}/{date}', [TimeController::class, 'deleteBusinessTripTime']);
    Route::post('/ajouter-heure-non-facturee', [TimeController::class, 'addUnbilledInterventionTime']);
    Route::delete('/supprimer-heure-non-facturee/{date}', [TimeController::class, 'deleteUnbilledInterventionTime']);

    Route::get('/parametres/emails', [EmailTemplateController::class, 'emailSettings'])->name('email-settings');
    Route::put('/parametres/emails', [EmailTemplateController::class, 'update'])->name('email-settings.update');

    // Début [SPECGT6] - Service de gestion des paramètres
    Route::resource('parameters', ParameterController::class);
    Route::get('/parameters', [ParameterController::class, 'show']);
    Route::resource('loadouts', LoadoutController::class);
    Route::get('/loadouts/{id}', [LoadoutController::class, 'show']);
    Route::get('/manage', [ManageController::class, 'index'])->name('parameters.manage');

    Route::get('/modifier-parameter/{idChantier}/{idParameter}/{checked}', [ChantierController::class, 'updateParameter'])->name('chantier.updateParameter');
    // Fin [SPECGT6] - Service de gestion des paramètres
    Route::post('/users/coefProd', [UserController::class, 'patchCoef'])->name('users.coefProd');

    // Routes pour les événements personnalisés
    Route::post('/custom-events', [CustomEventController::class, 'store'])->name('custom-events.store');
    Route::put('/custom-events/{customEvent}', [CustomEventController::class, 'update'])->name('custom-events.update');
    Route::delete('/custom-events/{customEvent}', [CustomEventController::class, 'destroy'])->name('custom-events.destroy');

    // Avatar
    Route::post('/avatar', [AvatarController::class, 'store'])->name('avatar.store');
    Route::delete('/avatar/{avatar}', [AvatarController::class, 'destroy'])->name('avatar.destroy');
});
require __DIR__ . '/auth.php';
