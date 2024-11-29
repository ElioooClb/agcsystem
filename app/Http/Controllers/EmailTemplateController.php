<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EmailTemplate;
use Illuminate\Contracts\View\View;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * [SPECGT3]
     * @return View
     */
    public function emailSettings(): View
    {
        $emails = EmailTemplate::all();
        return view('emails.email-settings', compact('emails'));
    }

    /**
     * Update the specified email template.
     * [SPECGT3]
     * @param Request $request - The request keys: ['id', 'type', 'label', 'email']
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string',
            'label' => 'required|string',
            'email' => 'required|email',
        ]);

        EmailTemplate::whereId($validatedData['id'])->update($validatedData);
        return response()->json(['message' => 'Le template a été mis à jour avec succès']);
    }
}

