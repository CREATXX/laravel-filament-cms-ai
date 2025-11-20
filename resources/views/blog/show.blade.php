@extends('layouts.app')

@section('title', $post->seo_title ?? $post->title)
@section('meta_description', $post->meta_description)
@section('meta_keywords', $post->meta_keywords)
@section('og_title', $post->seo_title ?? $post->title)
@section('og_description', $post->meta_description)
@section('og_image', $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/og-image.jpg'))
@section('og_type', 'article')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Ana Sayfa', 'url' => route('home')],
        ['label' => 'Blog', 'url' => route('blog.index')],
        ['label' => $post->title]
    ]" />
    
    <article class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            {{-- Post Header --}}
            <header class="mb-8">
                <div class="flex items-center space-x-2 mb-4">
                    @if($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="inline-block bg-blue-600 text-white text-xs px-3 py-1 rounded-full hover:bg-blue-700 transition">
                            {{ $post->category->name }}
                        </a>
                    @endif
                    
                    @if($post->is_ai_generated)
                        <span class="inline-block bg-purple-600 text-white text-xs px-3 py-1 rounded-full">
                            ✨ AI Üretildi
                        </span>
                    @endif
                    
                    @if($post->seo_score >= 70)
                        <span class="inline-block bg-green-600 text-white text-xs px-3 py-1 rounded-full">
                            SEO: {{ $post->seo_score }}/100
                        </span>
                    @endif
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $post->title }}</h1>
                
                <div class="flex items-center text-gray-600 text-sm space-x-4">
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
                        {{ $post->reading_time }} dk okuma
                    </span>
                    
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $post->view_count }} görüntülenme
                    </span>
                </div>
            </header>
            
            {{-- Featured Image --}}
            @if($post->featured_image)
                <div class="mb-8 rounded-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-auto">
                </div>
            @endif
            
            {{-- Post Content --}}
            <div class="prose prose-lg max-w-none mb-12">
                {!! $post->content !!}
            </div>
            
            {{-- Tags --}}
            @if($post->tags)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Etiketler</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <span class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded-full">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Social Share --}}
            <x-social-share :title="$post->title" :url="route('blog.show', $post->slug)" />
            
            {{-- Post Navigation --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-12">
                @if($previousPost)
                    <a href="{{ route('blog.show', $previousPost->slug) }}" class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition group">
                        <p class="text-xs text-gray-500 mb-2">← Önceki Yazı</p>
                        <h4 class="font-semibold group-hover:text-blue-600 transition">{{ $previousPost->title }}</h4>
                    </a>
                @endif
                
                @if($nextPost)
                    <a href="{{ route('blog.show', $nextPost->slug) }}" class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition group text-right">
                        <p class="text-xs text-gray-500 mb-2">Sonraki Yazı →</p>
                        <h4 class="font-semibold group-hover:text-blue-600 transition">{{ $nextPost->title }}</h4>
                    </a>
                @endif
            </div>
            
            {{-- Related Posts --}}
            @if($relatedPosts->isNotEmpty())
                <div class="border-t border-gray-200 pt-12">
                    <h3 class="text-2xl font-bold mb-6">İlgili Yazılar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $relatedPost)
                            <x-post-card :post="$relatedPost" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>
@endsection
