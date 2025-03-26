<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductionOrder;

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
    private function daysOfStockAvailable($product_id)
    {
        $product = Product::with('bomItems.material')->findOrFail($product_id);
        foreach ($product->bomItems as $bomItem) {
            
            $material = $bomItem->material;
            if ($material && $bomItem->input_material_type === 'materials' ) {
                $this->info("ID: {$material->id} | Số lượng: {$bomItem->quantity_input}");
            }
        }
    }

    
    public function handle()
    {
        
        $products = Product::all();
        foreach($products as $product){
            $this->info("Số nguyên vật liệu cần");
            $this->daysOfStockAvailable($product->id);
        }
        
    }

}
