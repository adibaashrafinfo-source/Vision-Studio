<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $staticRoutes = ['home', 'products.index', 'faq', 'shipping', 'returns', 'privacy', 'terms', 'stores', 'help'];
        foreach ($staticRoutes as $name) {
            $urls[] = ['loc' => route($name), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => $name === 'home' ? '1.0' : '0.6'];
        }

        Category::active()->get()->each(function ($c) use (&$urls) {
            $urls[] = ['loc' => route('category.show', $c), 'lastmod' => $c->updated_at->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.7'];
        });

        Product::active()->get()->each(function ($p) use (&$urls) {
            $urls[] = ['loc' => route('product.show', $p), 'lastmod' => $p->updated_at->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.8'];
        });

        BlogPost::published()->get()->each(function ($post) use (&$urls) {
            // Blog is admin-only right now (no public show route); include once that lands.
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n    <loc>".htmlspecialchars($u['loc'])."</loc>\n    <lastmod>{$u['lastmod']}</lastmod>\n    <changefreq>{$u['changefreq']}</changefreq>\n    <priority>{$u['priority']}</priority>\n  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
