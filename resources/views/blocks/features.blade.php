{{-- Features Block --}}
<section class="features-section py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            @if(isset($data['section_title']))
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
                    {{ $data['section_title'] }}
                </h2>
            @endif
            
            @if(isset($data['section_description']))
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ $data['section_description'] }}
                </p>
            @endif
        </div>
        
        @if(isset($data['features']) && count($data['features']) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($data['features'] as $feature)
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                        @if(isset($feature['icon']))
                            <div class="mb-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {{-- Icon render edilecek --}}
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        @endif
                        
                        @if(isset($feature['title']))
                            <h3 class="text-xl font-bold mb-3 text-gray-900">
                                {{ $feature['title'] }}
                            </h3>
                        @endif
                        
                        @if(isset($feature['description']))
                            <p class="text-gray-600">
                                {{ $feature['description'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
