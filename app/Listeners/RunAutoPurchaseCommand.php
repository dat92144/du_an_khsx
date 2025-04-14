<?php

namespace App\Listeners;

use App\Events\ProductionOrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
class RunAutoPurchaseCommand
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductionOrderCreated $event): void
    {
        Artisan::call('purchase:auto-production');
    }
}
