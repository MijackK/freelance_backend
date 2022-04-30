<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Events\Messages;
use App\Models\User;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user()->id;
        $messages = DB::table('messages')
        ->where('to',$user)
        ->orWhere('from',$user)
        ->join('users as sender','messages.from','=','sender.id')
        ->join('users as reciever','messages.to','=','reciever.id')
        ->select('sender.name as from','reciever.name as to','messages.text','messages.created_at as date','reciever.id as idTo','sender.id as idFrom','reciever.avatar as ravatar','sender.avatar as savatar')
        ->get();

        return [$messages,auth()->user()->name,auth()->user()->id];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::table('messages')->insert([
            'from'=>auth()->user()->id,
            'to'=>$request->input('to'),
            'text'=>$request->input('text'),
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now(),
        ]);

        DB::table('notifications')->insert([
            'user_id'=>$request->input('to'),
            'avatar'=>User::where('id',auth()->user()->id)->value('avatar'),
            'title'=>auth()->user()->name." Sent you a Message",
            'content' =>substr($request->input('text'),0,20),
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now(),
        ]); 

        $object = (object) ['name'=> auth()->user()->name,'user_id' => $request->input('to')];
        
        broadcast(new Messages($object)); 

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        
    }
 
}
