<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (Review::exists()) {
            return;
        }

        $customers = Customer::all();
        if ($customers->isEmpty()) {
            return;
        }

        $reviews = [
            ['product' => 'ray-ban-rx5121-square-optical-frame', 'rating' => 5, 'title' => 'Exactly what I wanted', 'comment' => 'Lightweight and the prescription fit was spot on. Delivery took 2 days to Dhanmondi.', 'status' => 'approved'],
            ['product' => 'ray-ban-wayfarer-classic', 'rating' => 5, 'title' => 'Genuine and well packaged', 'comment' => 'Came with the real Ray-Ban box and card. Looks and feels authentic.', 'status' => 'approved'],
            ['product' => 'ray-ban-wayfarer-classic', 'rating' => 4, 'title' => 'Great sunglasses, slow delivery', 'comment' => 'Product quality is excellent, took a bit longer than expected to arrive in Sylhet.', 'status' => 'approved'],
            ['product' => 'oakley-plutonite-sport-shield', 'rating' => 5, 'title' => 'Perfect for cycling', 'comment' => 'No slipping even during hot, sweaty rides. Worth the price.', 'status' => 'approved'],
            ['product' => 'vision-studio-blue-light-block-rectangle-frame', 'rating' => 4, 'title' => 'Eyes feel less tired', 'comment' => 'Wearing these during work calls all day, noticeably less strain by evening.', 'status' => 'approved'],
            ['product' => 'vision-studio-heritage-round-floral-acetate', 'rating' => 5, 'title' => 'Compliments every time', 'comment' => 'So many people have asked where I got these. Comfortable too.', 'status' => 'approved'],
            ['product' => 'oakley-holbrook-rx-square-frame', 'rating' => 3, 'title' => 'Good but pricey', 'comment' => 'Quality is there but I expected a cheaper price for an optical-only frame.', 'status' => 'pending'],
            ['product' => 'police-urban-flat-top-frame', 'rating' => 5, 'title' => 'Sharp look for office', 'comment' => 'Gets compliments at work, sturdy hinges too.', 'status' => 'pending'],
            ['product' => 'acuvue-oasys-monthly-contact-lenses', 'rating' => 4, 'title' => 'Comfortable all day', 'comment' => 'No dryness even after 10+ hours. Will reorder.', 'status' => 'approved'],
        ];

        foreach ($reviews as $i => $data) {
            $product = Product::where('slug', $data['product'])->first();
            if (! $product) {
                continue;
            }

            Review::create([
                'product_id' => $product->id,
                'customer_id' => $customers[$i % $customers->count()]->id,
                'author_name' => $customers[$i % $customers->count()]->name,
                'rating' => $data['rating'],
                'title' => $data['title'],
                'comment' => $data['comment'],
                'status' => $data['status'],
            ]);
        }
    }
}
