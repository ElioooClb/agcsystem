<?php

namespace App\Http\Controllers;

use App\Models\CustomEvent;
use Illuminate\Http\Request;

class CustomEventController extends Controller
{
    /**
     * Store a newly created custom event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date',
            'allDay' => 'boolean',
            'backgroundColor' => 'nullable|string|max:7',
            'borderColor' => 'nullable|string|max:7',
            'textColor' => 'nullable|string|max:7',
            'url' => 'nullable|url',
            'extendedProps' => 'nullable|array'
        ]);

        $customEvent = CustomEvent::create($validated);

        return response()->json($customEvent);
    }

    /**
     * Update the specified custom event in storage.
     */
    public function update(Request $request, CustomEvent $customEvent)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date',
            'allDay' => 'boolean',
            'backgroundColor' => 'nullable|string|max:7',
            'borderColor' => 'nullable|string|max:7',
            'textColor' => 'nullable|string|max:7',
            'url' => 'nullable|url',
            'extendedProps' => 'nullable|array'
        ]);

        $customEvent->update($validated);

        return response()->json($customEvent);
    }

    /**
     * Remove the specified custom event from storage.
     */
    public function destroy(CustomEvent $customEvent)
    {
        $customEvent->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
