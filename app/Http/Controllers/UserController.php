<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('role')->get();
        return view('users.gestion.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.gestion.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Form validation

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'fonction' => 'required',
            'role_id' => 'required',
            'password' => 'required|min:8',
            'acronyme' => 'required',
        ]);

        //  Store data in database
        $user = new User;
        $user->name = $request->input('name');
        $user->email = trim($request->input('email'));
        $user->password = bcrypt($request->input('password'));
        $user->fonction = $request->input('fonction');
        $user->acronyme = $request->input('acronyme');
        $user->role_id = $request->input('role_id');
        $user->save();

        return redirect()->route('users.index')->withStatus('Le collaborateur a bien été créé !');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->email = null;
        $user->password = null;
        $user->role_id = 3;
        $user->save();

        return redirect()->route('users.index')->withStatus('Le collaborateur a bien été supprimé !');
    }

    public function edite(User $user)
    {
        $roles = Role::all();
        return view('users.gestion.edite', [
            'roles' => $roles,
            'user' => $user,

        ]);
    }

    public function update(Request $request, User $user)
    {
        // Form validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'fonction' => 'required',
            'role_id' => 'required',
            'acronyme' => 'required',

        ]);

        $user->name = $request->input('name');
        $user->email = trim($request->input('email'));
        $user->fonction = $request->input('fonction');
        $user->role_id = $request->input('role_id');
        $user->acronyme = $request->input('acronyme');


        $user->save();
        return redirect()->route('users.index')->withStatus('Le collaborateur a bien été édité !');
    }

    public function updatePassword(Request $request, $User)
    {
        $request->validate([
            'password' => 'required',
            // 'password_confirmation' => 'required',

        ]);

        User::findOrFail($User)->update([
            'password' => Hash::make($request['password'])

        ]);

        return redirect()->route('users.index')->withStatus('Le mot de passe a bien été édité !');
    }

    /**
     * Update the specified resource in storage.
     * @version 1 - 2021-09-07 [SPECMBA06]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function patchCoef(Request $request)
    {
        $request->validate([
            'coef' => 'required',
            'user_id' => 'required',
        ]);

        $admin = Auth::user();
        if ($admin->fonction !== 'Président') {
            return redirect()->back()->withErrors('Vous n\'avez pas les droits pour effectuer cette action');
        }

        $user = User::findOrFail($request->user_id);
        $user->coef_prod = $request->coef;
        $user->save();
        return redirect()->route('users.index')->withStatus('Le coefficient a bien été édité !');
    }
}
