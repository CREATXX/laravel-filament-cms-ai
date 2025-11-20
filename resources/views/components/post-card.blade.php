@props(['post'])

<article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
    {{-- Featured Image --}}
    <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden">
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </a>
    
    {{-- Content --}}
    <div class="p-5">
        {{-- Category & Badges --}}
        <div class="flex items-center space-x-2 mb-3">
            @if($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">
                    {{ $post->category->name }}
                </a>
            @endif
            
            @if($post->is_ai_generated)
                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded">
                    ✨ AI
                </span>
            @endif
            
            @if($post->seo_score >= 70)
                <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">
                    SEO {{ $post->seo_score }}
                </span>
            @endif
        </div>
        
        {{-- Title --}}
        <h3 class="font-bold text-lg mb-2 group-hover:text-blue-600 transition line-clamp-2">
            <a href="{{ route('blog.show', $post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>
        
        {{-- Excerpt --}}
        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
            {{ $post->excerpt }}
        </p>
        
        {{-- Meta Info --}}
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $post->published_at?->format('d.m.Y') }}
            </span>
            
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $post->reading_time }} dk
            </span>
        </div>
    </div>
</article>
