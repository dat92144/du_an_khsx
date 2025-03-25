<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
class AutoPurchase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-purchase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    private function daysOfStockAvailable($product_id){
        $product = Product::with('BomItem')->findOrFail($product_id)->get();
        foreach($product->material as $material){
            if($material->input_type === 'material'){
                $this->info($material->material_id, 'số lượng: ', $material->quantity);
            }
        }
    }
    
    public function handle()
    {
        //
    }

}
