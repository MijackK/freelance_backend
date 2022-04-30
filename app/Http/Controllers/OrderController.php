<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Job;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\OrderRequest;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user()->id;
       $orders =User::find($user)->orders()
       ->join('users','orders.buyer_id','=','users.id')
       ->join('jobs as gigs','orders.job_id','=','gigs.id')
       ->select('orders.id','orders.predicted_at','orders.status','orders.request','users.avatar','users.name as user_name','gigs.name as job_name',)
       ->get();

       return $orders;
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validattions(Request $request){
        $user = auth()->user()->id;
        $check=DB::table('orders')->where('buyer_id', $user)
        ->where('job_id',$request->input('id'))->count();

        $owner_id = Job::find($request->input('id'))->user()->value('id');

        if($user !== $owner_id and $check == 0 ){
            $response = Http::get('https://randomuser.me/api');
            return $response;
        }
        else{
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user()->id;
        $check=DB::table('orders')->where('buyer_id', $user)
        ->where('job_id',$request->input('id'))->count();

        $owner_id = Job::find($request->input('id'))->user()->value('id');
    

         if($user !== $owner_id and $check == 0 ){
            DB::table('orders')->insert([
                'job_id'=>$request->input('id'),
                'buyer_id'=>$user,
                'request'=>$request->input('requirments'),
                'status'=>'pending',
                'created_at' => Carbon::now(),
                'updated_at' =>Carbon::now(),
            ]);
             
            DB::table('notifications')->insert([
                'user_id'=>$owner_id,
                'avatar'=>User::where('id',$user)->value('avatar'),
                'title'=>User::where('id',$user)->value('name') ." Requests a Job",
                'job_name'=> Job::where('id',$request->input('id'))->value('name'),
                'content' =>$request->input('requirments'),
                'created_at' => Carbon::now(),
                'updated_at' =>Carbon::now(),
            ]); 
            $object = (object) ['name'=>User::where('id',$user)->value('name') ,'user_id' =>$owner_id ,'content'=>'Requests a job'];
        
            broadcast(new OrderRequest($object));  
            return 'Request Sucessful';
         
              
         }
         else{
        return 'This is you job or you have already requested this job';
         }
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function showHired(Order $order)
    {
        $user = auth()->user()->id;
        
        
        return User::find($user)->hired()
        ->wherein('status',['pending','accepted'])
        ->join('jobs','orders.job_id','=','jobs.id')
        ->join('users','jobs.user_id','=','users.id')
        ->select('orders.id','orders.predicted_at','orders.status','users.avatar','users.name as user_name','jobs.name as job_name','orders.request','jobs.price','jobs.thumbnail')
        ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
    public function request(Request $request)
    {
        $update =DB::table('orders')
        ->where('id',$request->input('order'))
        ->update(['status'=>$request->input('status'),
        'updated_at'=>Carbon::now(), 
        'predicted_at'=>Carbon::now()->add(5, 'day'),]);
        echo 'updated';

        $reciever_id=Order::where('id',$request->input('order'))->value('buyer_id');
        $user = auth()->user()->id;

        DB::table('notifications')->insert([
            'user_id'=>$reciever_id,
            'avatar'=>User::where('id',$user)->value('avatar'),
            'title'=>User::where('id',$user)->value('name')." ".$request->input('status')." your Request",
            'job_name'=> Order::find($request->input('order'))->job()->value('name'),
            'content' =>Order::where('id',$request->input('order'))->value('request'),
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now(),
        ]); 
        if($request->input('status')=="accepted"){
            DB::table('messages')->insert([
                'from'=>$reciever_id,
                'to'=>$user,
                'text'=>Order::where('id',$request->input('order'))->value('request'),
                'created_at' => Carbon::now(),
                'updated_at' =>Carbon::now()]);
        }
        if($request->input('status')=="completed" or $request->input('status')=="cancelled" ){
            
            DB::table('transactions')->insert([
            'user_id'=>$reciever_id,
            'seller_id'=>$user,
            'order_id'=>$request->input('order'),
            'price'=>Order::find($request->input('order'))->job()->value('price'),
            'name'=>Order::find($request->input('order'))->job()->value('name'),
            'date'=>Carbon::now(),
        ]);

        }


        $object = (object) ['name'=> User::where('id',$user)->value('name'),'user_id' => $reciever_id,'content'=>$request->input('status').' your Request'];
        
        broadcast(new OrderRequest($object));  
  
    }

   

 
}
