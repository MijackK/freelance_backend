<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Job;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()

    {
        return User::find(auth()->user()->id)->reviews()
        ->join('users','reviews.user_id','=','users.id')
        ->select('reviews.*','users.name','users.avatar')
        ->get();
        
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
        $Review = new Review;

        $order_id=Job::find($request->input('job_id'))
        ->orders()
        ->where('buyer_id',auth()->user()->id)
        ->value('id');

        
        $check =DB::table('transactions')->where('user_id',auth()->user()->id)
        ->where('order_id',$order_id)->value('id');

        $check2=DB::table('reviews')->where('job_id',$request->input('job_id'))
        ->where('user_id',auth()->user()->id)->value('id');

        if(!$check){
            abort(403);
        }
        if($check2){
            return 'Review already posted';
        }

  

        if(!empty($request->title) and !empty($request->content) and !empty($request->score)){
            $Review->user_id = auth()->user()->id;
            $Review->job_id = $request->input('job_id');
            $Review->title = $request->input('title');
            $Review->content = $request->input('content');
            $Review->score = $request->input('score');
            $Review->save();

            return "review posted";
        }
        else{
            return 'Could not Post Review';
        }

      
       

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function getReview( $id)
    {
        return Review::where('job_id',$id)
        ->join('users','reviews.user_id','=','users.id')
        ->select('reviews.*','users.avatar','users.name')
        ->get();
    }

    public function userReview( $id)
    {
        return User::find($id)->reviews()
        ->join('users','reviews.user_id','=','users.id')
        ->select('reviews.*','users.name','users.avatar')
        ->get();
    }
}
