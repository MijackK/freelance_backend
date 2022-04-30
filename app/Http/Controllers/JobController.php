<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Events\Hello;
use App\Events\JobRequest;


class JobController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
       $user = auth()->user()->id;
       $jobs = DB::table('jobs')
       ->where('user_id',$user)
       ->get();
       return $jobs;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userJobs()
    {
        $jobs= User::find(auth()->user()->id)->jobs()
        ->join('users','jobs.user_id','=','users.id')
        ->select('jobs.*','users.avatar','users.name as user_name')
        ->get();
        
       $job_id =Job::where('user_id',auth()->user()->id)->select('id')->get();
       $review_score=array();
       $review_count=array();

       foreach ($job_id as $review) {
        array_push($review_score,Job::find($review->id)->reviews()->avg('score'));
        array_push( $review_count,Job::find($review->id)->reviews()->count('score'));
      } 
   

       return [$jobs,$review_score,$review_count];

    }


    public function profilejobs($job){
        $jobs= User::find($job)->jobs()
        ->join('users','jobs.user_id','=','users.id')
        ->select('jobs.*','users.avatar','users.name as user_name')
        ->get();

        $job_id =Job::where('user_id',$job)->select('id')->get();
        $review_score=array();
        $review_count=array();
 
        foreach ($job_id as $review) {
         array_push($review_score,Job::find($review->id)->reviews()->avg('score'));
         array_push( $review_count,Job::find($review->id)->reviews()->count('score'));
       } 
    
 
        return [$jobs,$review_score,$review_count];
    }
    public function jobProfile($job){
        return DB::table('jobs')->where('jobs.id',$job)
        ->join('users','jobs.user_id','=','users.id')
        ->select('jobs.*','users.avatar','users.name as user_name','users.id as user_id')
        ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //change this to use eloquents save()
        $name = $request->input('name');
        $description = $request->input('description');
        $price = $request->input('price');
        $category=$request->input('category');
        $thumbnail=$request->input('thumbnail');
        $user = auth()->user()->id;
        $object = (object) ['name'=> $name,'user_id' => $user,'type'=>'create'];
        if(!empty($name) and !empty($description) and !empty($price) and !empty($category)and !empty($thumbnail)){
            DB::table('jobs')->insert([
                'name'=>$name,
                'description'=>$description,
                'price'=> $price,
                'category_id'=>$category,
                'thumbnail'=>$thumbnail,
                'user_id'=> $user,
                'created_at' => Carbon::now(),
                'updated_at' =>Carbon::now(),
            ]); 
            DB::table('notifications')->insert([
                'user_id'=>$user,
                'avatar'=>User::where('id',$user)->value('avatar'),
                'title'=>"Your Job Got posted",
                'job_name'=> $name,
                'content' =>$description,
                'created_at' => Carbon::now(),
                'updated_at' =>Carbon::now(),
            ]); 
            
            broadcast(new JobRequest($object));  
           
        }
        else {
            return 'one or more fields are empty';
        } 
     
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
       
        $jobs = DB::table('jobs')
        ->join('users','jobs.user_id','=','users.id')
        ->select('jobs.*','users.avatar','users.name as user_name')
       ->get();

       $job_id =Job::select('id')->get();
       $review_score=array();
       $review_count=array();
       $order_count=array();

       foreach ($job_id as $review) {
        array_push($review_score,Job::find($review->id)->reviews()->avg('score'));
        array_push( $review_count,Job::find($review->id)->reviews()->count('score'));
        array_push( $order_count,Order::where('job_id',$review->id)->count());
      } 
   

       return [$jobs,$review_score,$review_count,$order_count];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
       

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $price = $request->input('price');
        $job_id=$request->input('job');
        $check=Job::find($job_id)->user()->value('id');
        if($check != auth()->user()->id){
            abort(403);

        }
        if(!empty($name) and !empty($description) and !empty($price)){
            $update =DB::table('jobs')
            ->where('id',$job_id)
            ->update(['name'=>$name,'description'=>$description,'price'=>$price,'updated_at'=>Carbon::now()]);
        }
        else {
            return 'you need to update everything for now';
        } 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy($job)
    {
        $check=Job::find($job)->user()->value('id');
        if($check == auth()->user()->id ){
        DB::table('jobs')
        ->where('id',$job)
        ->delete();
        return 'deleted';
    }
   


    }
}
