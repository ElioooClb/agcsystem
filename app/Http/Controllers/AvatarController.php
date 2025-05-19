<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvatarController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();
        dump($user);
        dump($request->avatar);
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                $oldAvatarPath = public_path('storage/avatars/' . $user->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }

            // Store new avatar
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);

            // Update user avatar in database
            $user->avatar = $avatarName;
            $user->save();

            return redirect()->back()->withStatus('Avatar mis à jour avec succès !');
        }

        return redirect()->back()->withError('Une erreur est survenue lors du téléchargement.');
    }

    public function destroy()
    {
        $user = Auth::user();

        if ($user->avatar) {
            // Delete avatar file
            $avatarPath = public_path('storage/avatars/' . $user->avatar);
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }

            // Remove avatar from database
            $user->avatar = null;
            $user->save();

            return redirect()->back()->withStatus('Avatar supprimé avec succès !');
        }

        return redirect()->back()->withError('Aucun avatar à supprimer.');
    }
}
