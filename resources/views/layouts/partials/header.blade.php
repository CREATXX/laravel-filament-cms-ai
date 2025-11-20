<header class="bg-white shadow-sm sticky top-0 z-50">
    <nav class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            {{-- Logo --}}
            <div class="flex items-center space-x-2">
                @if($logo = App\Models\Setting::get('site_logo'))
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ App\Models\Setting::get('site_name') }}" class="h-10">
                @else
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                        {{ App\Models\Setting::get('site_name', 'CMS AI') }}
                    </a>
                @endif
            </div>
            
            {{-- Navigation Menu --}}
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 transition {{ request()->routeIs('home') ? 'text-blue-600 font-semibold' : '' }}">
                    Ana Sayfa
                </a>
                
                <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-blue-600 transition {{ request()->routeIs('blog.*') ? 'text-blue-600 font-semibold' : '' }}">
                    Blog
                </a>
                
                {{-- Dinamik Sayfalar --}}
                @php
                    $headerPages = App\Models\Page::published()
                        ->whereNotIn('slug', ['ana-sayfa', 'home'])
                        ->orderBy('created_at', 'asc')
                        ->limit(3)
                        ->get();
                @endphp
                
                @foreach($headerPages as $page)
                    <a href="{{ route('page.show', $page->slug) }}" class="text-gray-700 hover:text-blue-600 transition">
                        {{ $page->title }}
                    </a>
                @endforeach
            </div>
            
            {{-- Mobile Menu Button --}}
            <button class="md:hidden p-2" id="mobile-menu-button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
        
        {{-- Mobile Menu --}}
        <div class="hidden md:hidden mt-4 space-y-2" id="mobile-menu">
            <a href="{{ route('home') }}" class="block py-2 text-gray-700 hover:text-blue-600">Ana Sayfa</a>
            <a href="{{ route('blog.index') }}" class="block py-2 text-gray-700 hover:text-blue-600">Blog</a>
            @foreach($headerPages as $page)
                <a href="{{ route('page.show', $page->slug) }}" class="block py-2 text-gray-700 hover:text-blue-600">
                    {{ $page->title }}
                </a>
            @endforeach
        </div>
    </nav>
</header>

@push('scripts')
<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
@endpush
