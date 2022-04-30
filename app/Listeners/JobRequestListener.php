<?php

namespace App\Listeners;

use App\Events\JobRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class JobRequestListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  JobRequest  $event
     * @return void
     */
    public function handle(JobRequest $event)
    {
        //
    }
}
