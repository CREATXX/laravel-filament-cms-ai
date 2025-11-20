<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Blog liste sayfası
     */
    public function index()
    {
        $posts = Post::published()
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();
        
        $popularPosts = Post::published()
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('blog.index', compact('posts', 'categories', 'popularPosts'));
    }
    
    /**
     * Kategoriye göre blog listesi
     */
    public function category(string $category)
    {
        $categoryModel = Category::where('slug', $category)->firstOrFail();
        
        $posts = Post::published()
            ->category($category)
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();
        
        $popularPosts = Post::published()
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('blog.index', compact('posts', 'categories', 'popularPosts', 'categoryModel'));
    }
    
    /**
     * Blog detay sayfası
     */
    public function show(string $slug)
    {
        $post = Post::published()
            ->with(['category'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Görüntülenme sayısını artır
        $post->incrementViewCount();
        
        // İlgili yazılar (aynı kategoriden)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, function ($query) use ($post) {
                $query->where('category_id', $post->category_id);
            })
            ->limit(3)
            ->get();
        
        // İlgili yazı bulunamazsa, son yazılardan getir
        if ($relatedPosts->isEmpty()) {
            $relatedPosts = Post::published()
                ->where('id', '!=', $post->id)
                ->limit(3)
                ->get();
        }
        
        // Önceki ve sonraki yazı
        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->first();
        
        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first();
        
        return view('blog.show', compact('post', 'relatedPosts', 'previousPost', 'nextPost'));
    }
}
