<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chantier;

use App\Http\Requests\StoresuiviRequest;
use App\Http\Requests\UpdatesuiviRequest;

class SuiviController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $user = User::find($user->id);
        return view('users.suivi.index',[
            'user' => $user,
        ]);
    }

    public function suivi(User $user)
    {
        $chantierIds = $user->chantiers()->pluck('chantier_id');
        $chantiers = Chantier::whereIn('id', $chantierIds)->get();


        return view('users.suivi.heureUser', [
            'user' => $user,
            'chantiers' => $chantiers,
        ]);
    }

    public function indexHeures()
    {
        return view('users.suivi.suivi');
    }
}
