<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $author = Admin::first();

        $posts = [
            [
                'title' => 'How to Choose the Right Frame for Your Face Shape',
                'category' => 'Style Guide',
                'excerpt' => 'A quick guide to matching frame shapes with round, square, oval and heart face shapes.',
                'content' => "Choosing a frame is about more than following a trend — the right shape balances your face.\n\n**Round faces** suit angular frames like square or rectangle shapes, which add definition.\n\n**Square faces** look great in round or oval frames that soften strong jawlines.\n\n**Oval faces** can wear almost anything — try a bold frame to make a statement.\n\n**Heart-shaped faces** pair well with frames that are wider at the bottom, like aviators or cat-eye shapes.\n\nVisit any Vision Studio location for a free fit consultation with our opticians.",
                'image' => '88fb23574ac9e31842ba8408f13cc0ad.png',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => '5 Signs You Need Blue-Light Glasses',
                'category' => 'Eye Care',
                'excerpt' => 'If you spend 6+ hours a day on a screen, these symptoms might be telling you something.',
                'content' => "Digital eye strain is becoming more common as work and study move online. Watch for:\n\n1. Headaches after long screen sessions\n2. Dry or irritated eyes by the afternoon\n3. Difficulty focusing on text\n4. Trouble sleeping after evening screen time\n5. Increased sensitivity to bright light\n\nBlue-light filtering lenses won't cure all of this, but combined with the 20-20-20 rule (every 20 minutes, look at something 20 feet away for 20 seconds), most people notice a real difference within a week.",
                'image' => '9a5fa854773ac05609dcb340e19f1dcd.png',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Daily vs. Monthly Contact Lenses: Which Is Right for You?',
                'category' => 'Contact Lenses',
                'excerpt' => 'Breaking down the cost, convenience and hygiene trade-offs between daily and monthly lenses.',
                'content' => "Both formats are safe when used correctly — the right choice usually comes down to lifestyle.\n\n**Daily disposables** are simplest: wear once, discard, no cleaning. Best for occasional wearers or anyone prone to eye irritation.\n\n**Monthly lenses** cost less per day for daily wearers, but need a proper cleaning routine with solution every night.\n\nIf you wear lenses fewer than 4 days a week, dailies usually work out cheaper overall. Our in-store opticians can help you calculate which works out better for your routine.",
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(4),
            ],
            [
                'title' => 'Vision Studio Is Opening a New Store in Chattogram',
                'category' => 'Announcements',
                'excerpt' => 'A draft announcement for our upcoming GEC Circle location — not yet public.',
                'content' => "We're excited to announce our newest store opening soon near GEC Circle, Chattogram. Final details on the opening date and launch offers are still being confirmed.",
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($posts as $data) {
            $slug = Str::slug($data['title']);
            $featuredImage = ! empty($data['image']) ? $this->copyBlogImage($data['image']) : null;

            BlogPost::updateOrCreate(['slug' => $slug], [
                'title' => $data['title'],
                'slug' => $slug,
                'category' => $data['category'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'featured_image' => $featuredImage,
                'status' => $data['status'],
                'author_id' => $author?->id,
                'published_at' => $data['published_at'],
            ]);
        }
    }

    protected function copyBlogImage(string $filename): ?string
    {
        $source = public_path('images/storefront/'.$filename);
        if (! is_file($source)) {
            return null;
        }

        $target = 'blog/'.$filename;
        if (! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, file_get_contents($source));
        }

        return $target;
    }
}
