<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixImageUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:image-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix image URLs by removing backslashes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all products
        $products = Product::all();

        // Iterate over each product and clean the image_url
        foreach ($products as $product) {
            $cleanedImageUrl = preg_replace('/\\\\/', '', $product->image_url);

            // Only update if the URL was actually changed
            if ($product->image_url !== $cleanedImageUrl) {
                $product->image_url = $cleanedImageUrl;
                $product->save();

                $this->info('Updated product ID ' . $product->id . ' with cleaned URL.');
            }
        }

        $this->info('All URLs have been fixed.');
        
        return 0;
    }
}
