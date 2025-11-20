<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Ana sayfa gösterimi
     */
    public function home()
    {
        $page = Page::published()
            ->whereIn('slug', ['ana-sayfa', 'home'])
            ->first();
        
        // Eğer ana sayfa bulunamazsa, ilk yayınlanan sayfayı göster
        if (!$page) {
            $page = Page::published()->first();
        }
        
        if (!$page) {
            abort(404, 'Ana sayfa bulunamadı');
        }
        
        return view('pages.show', compact('page'));
    }
    
    /**
     * Sayfa detay gösterimi
     */
    public function show(string $slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();
        
        return view('pages.show', compact('page'));
    }
    
    /**
     * Block rendering helper (static method for views)
     */
    public static function renderBlocks($content)
    {
        if (!is_array($content)) {
            return '';
        }
        
        $html = '';
        
        foreach ($content as $block) {
            if (isset($block['type']) && isset($block['data'])) {
                $html .= view('blocks.' . $block['type'], ['data' => $block['data']])->render();
            }
        }
        
        return $html;
    }
}
