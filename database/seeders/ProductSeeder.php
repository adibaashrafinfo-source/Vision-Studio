<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catId = Category::pluck('id', 'slug');

        $products = [
            // Eyeglasses
            [
                'name' => 'Ray-Ban RX5121 Square Optical Frame', 'brand' => 'Ray-Ban', 'category' => 'eyeglasses',
                'price' => 4200, 'compare_price' => 4800, 'stock_qty' => 24, 'featured' => true,
                'short_desc' => 'Timeless square acetate optical frame with the classic Ray-Ban signature.',
                'long_desc' => "A wardrobe staple reimagined as a prescription frame. The RX5121 keeps Ray-Ban's iconic square silhouette in a lightweight acetate build that suits most face shapes.\n\nComes with genuine Ray-Ban case and cleaning cloth. Lenses are cut and fitted to your prescription at checkout.",
                'image' => 'c168560a1d5b1d84eab8fac7c5f69ee3.png',
                'variants' => [['name' => 'Frame Color', 'value' => 'Gloss Black', 'price_delta' => 0, 'stock_qty' => 14], ['name' => 'Frame Color', 'value' => 'Smoke Grey', 'price_delta' => 150, 'stock_qty' => 10]],
            ],
            [
                'name' => 'Police Urban Flat-Top Frame', 'brand' => 'Police', 'category' => 'eyeglasses',
                'price' => 5200, 'compare_price' => null, 'stock_qty' => 16, 'featured' => false,
                'short_desc' => 'Bold flat-top acetate frame made in Italy, for a confident everyday look.',
                'long_desc' => "Police's Urban line pairs a flat brow line with a slightly rounded lens shape for a modern streetwear edge.\n\nMatte-finish acetate, spring hinges, and anti-slip temple tips keep it comfortable through long work days.",
                'image' => '329ca53d119b5380bdc5790ce04f8ec3.png',
            ],
            [
                'name' => 'Oakley Rocket Titanium Optical', 'brand' => 'Oakley', 'category' => 'eyeglasses',
                'price' => 6800, 'compare_price' => 7500, 'stock_qty' => 12, 'featured' => false,
                'short_desc' => 'Ultra-light titanium optical frame built for all-day wear.',
                'long_desc' => "Oakley's O Matter and titanium construction make the Rocket one of the lightest full-rim frames in our catalog. Adjustable nose pads give a secure, custom fit.",
                'image' => '4634f5bc89e921be37cff849aa4c95e6.png',
            ],
            [
                'name' => 'Oakley Holbrook RX Square Frame', 'brand' => 'Oakley', 'category' => 'eyeglasses',
                'price' => 6200, 'compare_price' => null, 'stock_qty' => 9, 'featured' => false,
                'short_desc' => 'The iconic Holbrook silhouette, built for prescription lenses.',
                'long_desc' => "Same bold, squared-off profile as the sunglasses original — now cut for clear optical lenses. A great pick for anyone who wants their daily glasses to make a statement.",
                'image' => '8c4783ea4f40ad529a76698c5e3129bb.png',
            ],
            [
                'name' => 'Vision Studio Heritage Gold-Trim Aviator', 'brand' => 'Vision Studio', 'category' => 'eyeglasses',
                'price' => 2800, 'compare_price' => null, 'stock_qty' => 30, 'featured' => false,
                'short_desc' => 'In-house aviator-shaped optical frame with gold-tone brow bar.',
                'long_desc' => "Our own house frame: a double-bridge aviator shape in matte black with a polished gold-tone brow, built on a durable metal alloy. Great value for a premium look.",
                'image' => '4a7362004b0c81f87f4fff63775c097b.png',
            ],
            [
                'name' => 'Vision Studio Heritage Round Floral Acetate', 'brand' => 'Vision Studio', 'category' => 'eyeglasses',
                'price' => 2600, 'compare_price' => null, 'stock_qty' => 22, 'featured' => true,
                'short_desc' => 'Round acetate frame in a hand-finished floral tortoise pattern.',
                'long_desc' => "A softer, rounder silhouette finished in a layered floral tortoise acetate — a popular pick for anyone wanting their glasses to double as a style statement.",
                'image' => 'fb5350cf1ac0a43dde198fe365bcc026.png',
                'variants' => [['name' => 'Frame Color', 'value' => 'Floral Tortoise', 'price_delta' => 0, 'stock_qty' => 12], ['name' => 'Frame Color', 'value' => 'Classic Black', 'price_delta' => 0, 'stock_qty' => 10]],
            ],

            // Sunglasses
            [
                'name' => 'Ray-Ban Wayfarer Classic', 'brand' => 'Ray-Ban', 'category' => 'sunglasses',
                'price' => 7200, 'compare_price' => null, 'stock_qty' => 20, 'featured' => true,
                'short_desc' => 'The original Wayfarer — gloss black acetate with G-15 green lenses.',
                'long_desc' => "No introduction needed. The Wayfarer Classic has been the industry standard since 1956. This set includes the genuine Ray-Ban hard case, cleaning cloth and authenticity card.",
                'image' => '88fb23574ac9e31842ba8408f13cc0ad.png',
            ],
            [
                'name' => 'Ray-Ban Meta Wayfarer Matte Black', 'brand' => 'Ray-Ban', 'category' => 'sunglasses',
                'price' => 8500, 'compare_price' => 9500, 'stock_qty' => 8, 'featured' => false,
                'short_desc' => 'Matte black Wayfarer with polarized gradient lenses.',
                'long_desc' => "A modern matte-black take on the Wayfarer silhouette with gradient-grey polarized lenses for reduced glare on bright days.",
                'image' => 'b7ae5b7ae0630fc15891992691adb7cc.png',
            ],
            [
                'name' => 'Ray-Ban Wayfarer Folding Black', 'brand' => 'Ray-Ban', 'category' => 'sunglasses',
                'price' => 7000, 'compare_price' => null, 'stock_qty' => 11, 'featured' => false,
                'short_desc' => 'Foldable Wayfarer for travel — same classic lens, half the pocket space.',
                'long_desc' => "Folds flat into a slim profile without compromising on the Wayfarer's signature look — ideal for travel or a bag that's already full.",
                'image' => 'c4b3dc6504b197b4a5bb6362087b5369.png',
            ],
            [
                'name' => 'Oakley Plutonite Sport Shield', 'brand' => 'Oakley', 'category' => 'sunglasses',
                'price' => 9800, 'compare_price' => null, 'stock_qty' => 7, 'featured' => true,
                'short_desc' => 'Wraparound sport shield with Plutonite lenses and 100% UV protection.',
                'long_desc' => "Built for movement — cycling, running or just Dhaka traffic glare. The wraparound shield design blocks peripheral light while Unobtainium grips keep it locked in place.",
                'image' => 'f89c2f40dce3541a3f5e57f2273d9f58.png',
            ],
            [
                'name' => 'Oakley Prizm Road Sport Sunglasses', 'brand' => 'Oakley', 'category' => 'sunglasses',
                'price' => 10500, 'compare_price' => 11800, 'stock_qty' => 5, 'featured' => false,
                'short_desc' => 'Prizm Road lenses tuned for contrast on bright, sunny rides.',
                'long_desc' => "Oakley's Prizm Road lens technology enhances road contrast for cyclists and drivers alike, set in a lightweight, grippy sport frame.",
                'image' => '2b67bf54298d459e4e5049685e107fc2.png',
            ],

            // Computer / blue-light glasses
            [
                'name' => 'Vision Studio Blue-Light Block Rectangle Frame', 'brand' => 'Vision Studio', 'category' => 'computer-glasses',
                'price' => 1800, 'compare_price' => null, 'stock_qty' => 40, 'featured' => true,
                'short_desc' => 'Lightweight rectangle frame with blue-light filtering lenses.',
                'long_desc' => "Designed for long hours in front of a screen. The blue-light filter reduces digital eye strain without noticeably tinting your view, in a slim unbranded metal frame.",
                'image' => '9a5fa854773ac05609dcb340e19f1dcd.png',
                'variants' => [['name' => 'Lens Type', 'value' => 'Blue-Light Filter', 'price_delta' => 0, 'stock_qty' => 25], ['name' => 'Lens Type', 'value' => 'Clear (No Filter)', 'price_delta' => -200, 'stock_qty' => 15]],
            ],
            [
                'name' => 'Vision Studio Anti-Glare Slim Aviator', 'brand' => 'Vision Studio', 'category' => 'computer-glasses',
                'price' => 1900, 'compare_price' => null, 'stock_qty' => 35, 'featured' => false,
                'short_desc' => 'Slim aviator-shaped computer glasses with anti-glare coating.',
                'long_desc' => "A slim, unbranded aviator shape in clear lenses with an anti-glare coating — comfortable for all-day desk work.",
                'image' => '5c9970a4625c8edc1764ea7460ee21c5.png',
            ],

            // Contact lenses — no studio photo available yet; storefront falls back to its default product image gracefully.
            [
                'name' => 'Acuvue Oasys Monthly Contact Lenses', 'brand' => 'Acuvue', 'category' => 'contact-lenses',
                'price' => 1500, 'compare_price' => null, 'stock_qty' => 60, 'featured' => false,
                'short_desc' => 'Monthly disposable lenses with HydraLuxe technology for all-day comfort.',
                'long_desc' => "A box of 6 lenses (one eye, one month each). HydraLuxe technology keeps the lens surface moist from the first blink to the last.",
            ],
            [
                'name' => 'Bausch + Lomb SofLens Daily Disposable', 'brand' => 'Bausch + Lomb', 'category' => 'contact-lenses',
                'price' => 1200, 'compare_price' => null, 'stock_qty' => 50, 'featured' => false,
                'short_desc' => 'Daily disposable lenses — fresh pair every day, no cleaning needed.',
                'long_desc' => "A box of 30 daily lenses. ComfortMoist technology keeps lenses hydrated through the day; simply discard after each use.",
            ],

            // Kids
            [
                'name' => 'Vision Studio Kids Flexible Silicone Frame', 'brand' => 'Vision Studio Kids', 'category' => 'kids-eyewear',
                'price' => 1500, 'compare_price' => null, 'stock_qty' => 28, 'featured' => false,
                'short_desc' => 'Bendable silicone frame built to survive active kids.',
                'long_desc' => "Virtually unbreakable silicone construction with soft, rounded edges — sized for children aged 4 to 10. Available in multiple bright colors.",
                'variants' => [['name' => 'Frame Color', 'value' => 'Sky Blue', 'price_delta' => 0, 'stock_qty' => 10], ['name' => 'Frame Color', 'value' => 'Coral Pink', 'price_delta' => 0, 'stock_qty' => 10], ['name' => 'Frame Color', 'value' => 'Lime Green', 'price_delta' => 0, 'stock_qty' => 8]],
            ],

            // Accessories
            [
                'name' => 'Premium Microfiber Lens Cleaning Kit', 'brand' => 'Vision Studio', 'category' => 'accessories',
                'price' => 350, 'compare_price' => null, 'stock_qty' => 80, 'featured' => false,
                'short_desc' => 'Microfiber cloth + anti-fog lens spray, travel-sized.',
                'long_desc' => "Everything you need to keep your lenses streak-free: a 30ml anti-fog, anti-static lens spray and a reusable microfiber cloth.",
            ],
            [
                'name' => 'Hard Shell Eyewear Case', 'brand' => 'Vision Studio', 'category' => 'accessories',
                'price' => 450, 'compare_price' => null, 'stock_qty' => 60, 'featured' => false,
                'short_desc' => 'Crush-resistant hard case with soft felt lining.',
                'long_desc' => "A compact, crush-resistant case lined with soft felt to keep frames scratch-free in a bag or glovebox.",
            ],
        ];

        foreach ($products as $data) {
            $categoryId = $catId[$data['category']] ?? null;
            $slug = Str::slug($data['name']);

            $product = Product::updateOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'slug' => $slug,
                'sku' => 'VS-'.Str::upper(Str::random(6)),
                'category_id' => $categoryId,
                'brand' => $data['brand'],
                'price' => $data['price'],
                'compare_price' => $data['compare_price'] ?? null,
                'stock_qty' => $data['stock_qty'],
                'status' => 'active',
                'featured' => $data['featured'],
                'short_desc' => $data['short_desc'],
                'long_desc' => $data['long_desc'],
            ]);

            if (! empty($data['image']) && $product->images()->doesntExist()) {
                $path = $this->copyProductImage($data['image']);
                if ($path) {
                    $product->images()->create(['image_path' => $path, 'is_primary' => true, 'position' => 1]);
                }
            }

            if (! empty($data['variants'])) {
                $product->variants()->delete();
                foreach ($data['variants'] as $variant) {
                    $product->variants()->create($variant);
                }
            }
        }

        // A couple of non-active products so the admin status filters have something to show.
        Product::updateOrCreate(['slug' => 'vision-studio-upcoming-titanium-frame'], [
            'name' => 'Vision Studio Upcoming Titanium Frame', 'slug' => 'vision-studio-upcoming-titanium-frame',
            'sku' => 'VS-'.Str::upper(Str::random(6)), 'category_id' => $catId['eyeglasses'] ?? null,
            'brand' => 'Vision Studio', 'price' => 5500, 'stock_qty' => 0, 'status' => 'draft', 'featured' => false,
            'short_desc' => 'Coming soon — new titanium frame in final QA.',
            'long_desc' => 'Not yet available for sale; kept here as a draft while photography and pricing are finalized.',
        ]);
        Product::updateOrCreate(['slug' => 'vision-studio-discontinued-reading-frame'], [
            'name' => 'Vision Studio Discontinued Reading Frame', 'slug' => 'vision-studio-discontinued-reading-frame',
            'sku' => 'VS-'.Str::upper(Str::random(6)), 'category_id' => $catId['eyeglasses'] ?? null,
            'brand' => 'Vision Studio', 'price' => 1200, 'stock_qty' => 0, 'status' => 'inactive', 'featured' => false,
            'short_desc' => 'Discontinued line, kept for order history only.',
            'long_desc' => 'This frame has been discontinued and is no longer sold.',
        ]);
    }

    /** Copy a bundled reference photo into public storage and return its relative path, or null if the source is missing. */
    protected function copyProductImage(string $filename): ?string
    {
        $source = public_path('images/storefront/'.$filename);
        if (! is_file($source)) {
            return null;
        }

        $target = 'products/'.$filename;
        if (! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, file_get_contents($source));
        }

        return $target;
    }
}
