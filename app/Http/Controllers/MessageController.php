<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Http\Request;


class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $message = Message::all();
        return view('message.index', compact('message'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('message.create');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required',
        ]);
        
        
        $message = new Message;
        $message->message = $request->input('message');
        $message->save();

        return redirect()->route('message.index')->withStatus('Le message a bien été créé !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        return view('message.edit',[
            'message' => $message,
           
       ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        $request->validate([
            'message' => 'required',
    
        ]);
    
        $message->message = $request->input('message');
    
    
        $message->save();
        return redirect()->route('message.index')->withStatus('Le message a bien été édité !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->destroy($id);

        return redirect()->route('message.index')->withStatus('Le message a bien été supprimé ');
    }


    public function publier(Request $request)
    {

        $id = $request->input('id');
        $slider = Message::findOrFail($id);
        $slider->is_published = !$slider->is_published;
        $slider->save();

        return response()->json($slider);

    }
}
