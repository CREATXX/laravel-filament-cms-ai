@extends('layouts.app')

@section('title', 'Blog - ' . App\Models\Setting::get('site_name'))
@section('meta_description', App\Models\Setting::get('site_description'))

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Ana Sayfa', 'url' => route('home')],
        ['label' => 'Blog']
    ]" />
    
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Blog Posts --}}
            <div class="lg:col-span-2">
                <h1 class="text-4xl font-bold mb-8">Blog Yazıları</h1>
                
                @if($posts->isEmpty())
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <p class="text-gray-600">Henüz blog yazısı bulunmuyor.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach($posts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <x-pagination :paginator="$posts" />
                @endif
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Categories --}}
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-bold mb-4">Kategoriler</h3>
                    <ul class="space-y-2">
                        @forelse($categories as $category)
                            <li>
                                <a href="{{ route('blog.category', $category->slug) }}" class="flex items-center justify-between text-gray-700 hover:text-blue-600 transition">
                                    <span>{{ $category->name }}</span>
                                    <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">
                                        {{ $category->posts_count }}
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500">Kategori bulunmuyor</li>
                        @endforelse
                    </ul>
                </div>
                
                {{-- Popular Posts --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Popüler Yazılar</h3>
                    <div class="space-y-4">
                        @forelse($popularPosts as $popularPost)
                            <a href="{{ route('blog.show', $popularPost->slug) }}" class="block group">
                                <div class="flex space-x-4">
                                    @if($popularPost->featured_image)
                                        <img src="{{ asset('storage/' . $popularPost->featured_image) }}" alt="{{ $popularPost->title }}" class="w-20 h-20 object-cover rounded">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-sm group-hover:text-blue-600 transition line-clamp-2">
                                            {{ $popularPost->title }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $popularPost->view_count }} görüntülenme
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-gray-500 text-sm">Popüler yazı bulunmuyor</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
