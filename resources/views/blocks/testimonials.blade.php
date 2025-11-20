{{-- Testimonials Block --}}
<section class="testimonials-section py-16 bg-gradient-to-br from-blue-50 to-purple-50">
    <div class="container mx-auto px-4">
        @if(isset($data['section_title']))
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center text-gray-900">
                {{ $data['section_title'] }}
            </h2>
        @endif
        
        @if(isset($data['testimonials']) && count($data['testimonials']) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($data['testimonials'] as $testimonial)
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            @if(isset($testimonial['avatar']) && $testimonial['avatar'])
                                <img src="{{ asset('storage/' . $testimonial['avatar']) }}" 
                                     alt="{{ $testimonial['author'] ?? '' }}"
                                     class="w-16 h-16 rounded-full object-cover mr-4">
                            @else
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <span class="text-2xl font-bold text-blue-600">
                                        {{ isset($testimonial['author']) ? substr($testimonial['author'], 0, 1) : '?' }}
                                    </span>
                                </div>
                            @endif
                            
                            <div>
                                @if(isset($testimonial['author']))
                                    <h4 class="font-bold text-gray-900">{{ $testimonial['author'] }}</h4>
                                @endif
                                
                                @if(isset($testimonial['position']))
                                    <p class="text-sm text-gray-600">{{ $testimonial['position'] }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($testimonial['quote']))
                            <blockquote class="text-gray-700 italic border-l-4 border-blue-500 pl-4">
                                "{{ $testimonial['quote'] }}"
                            </blockquote>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
