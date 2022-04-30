<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $transactions = DB::table('transactions')
       ->where('user_id',auth()->user()->id)
       ->orWhere('seller_id',auth()->user()->id)
       ->join('orders','transactions.order_id','=','orders.id')
       ->select('transactions.*','orders.status','orders.job_id')
       ->get();

       return $transactions;


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function receipt($transaction)
    {
        $check=Transaction::find($transaction);
        if(!$check){
            abort(403);
        }
        if(auth()->user()->id==$check->user_id or auth()->user()->id==$check->seller_id){
        $receipt = DB::table('transactions')
        ->where('transactions.id',$transaction)
        ->join('users as buyer','transactions.user_id','=','buyer.id')
        ->join('users as seller','transactions.seller_id','=','seller.id')
        ->join('orders','transactions.order_id','=','orders.id')
        ->select('transactions.name','transactions.price','transactions.date','buyer.name as bname','seller.name as sname','orders.status','orders.request')
        ->get();

        return $receipt;
      
        }
        else{
        abort(403);
        
        }
    
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
