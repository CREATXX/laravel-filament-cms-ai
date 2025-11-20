@extends('layouts.app')

@section('title', $page->seo_title ?? $page->title)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)
@section('og_title', $page->seo_title ?? $page->title)
@section('og_description', $page->meta_description)
@section('og_image', $page->featured_image ? asset('storage/' . $page->featured_image) : asset('images/og-image.jpg'))

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Ana Sayfa', 'url' => route('home')],
        ['label' => $page->title]
    ]" />
    
    {{-- Page Blocks --}}
    <div class="page-content">
        @if($page->content && is_array($page->content))
            @foreach($page->content as $block)
                @if(isset($block['type']) && isset($block['data']))
                    @include('blocks.' . $block['type'], ['data' => $block['data']])
                @endif
            @endforeach
        @else
            {{-- Fallback: Boş içerik --}}
            <div class="container mx-auto px-4 py-12">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <p class="text-yellow-700">Bu sayfa henüz içerik bloklarına sahip değil.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
