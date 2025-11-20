{{-- Hero Block --}}
<section class="hero-section relative bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20 md:py-32 overflow-hidden">
    @if(isset($data['background_image']) && $data['background_image'])
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('storage/' . $data['background_image']) }}" 
                 alt="Hero Background" 
                 class="w-full h-full object-cover opacity-20">
        </div>
    @endif
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            @if(isset($data['title']))
                <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">
                    {{ $data['title'] }}
                </h1>
            @endif
            
            @if(isset($data['subtitle']))
                <p class="text-xl md:text-2xl mb-8 text-gray-100 animate-fade-in-delay">
                    {{ $data['subtitle'] }}
                </p>
            @endif
            
            @if(isset($data['button_text']) && isset($data['button_url']))
                <a href="{{ $data['button_url'] }}" 
                   class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg">
                    {{ $data['button_text'] }}
                </a>
            @endif
        </div>
    </div>
</section>
