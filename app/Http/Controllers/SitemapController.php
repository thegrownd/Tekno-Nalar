<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->get();

        $content = view('sitemap', [
            'articles' => $articles,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
