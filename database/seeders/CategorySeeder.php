<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Eyeglasses', 'slug' => 'eyeglasses', 'icon' => '👓', 'description' => 'Prescription optical frames for everyday wear, from classic acetate to lightweight titanium.', 'position' => 1],
            ['name' => 'Sunglasses', 'slug' => 'sunglasses', 'icon' => '🕶️', 'description' => 'UV-protected sun frames from authentic international brands, built for Bangladeshi weather.', 'position' => 2],
            ['name' => 'Computer Glasses', 'slug' => 'computer-glasses', 'icon' => '💻', 'description' => 'Blue-light filtering lenses for screen-heavy days — less eye strain, clearer focus.', 'position' => 3],
            ['name' => 'Contact Lenses', 'slug' => 'contact-lenses', 'icon' => '👁️', 'description' => 'Daily and monthly disposable lenses from trusted global brands.', 'position' => 4],
            ['name' => 'Kids Eyewear', 'slug' => 'kids-eyewear', 'icon' => '🧒', 'description' => 'Flexible, durable frames sized and styled for children.', 'position' => 5],
            ['name' => 'Accessories', 'slug' => 'accessories', 'icon' => '🧰', 'description' => 'Cases, cleaning kits and everything else to keep your eyewear in top shape.', 'position' => 6],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], [...$category, 'status' => 'active']);
        }
    }
}
