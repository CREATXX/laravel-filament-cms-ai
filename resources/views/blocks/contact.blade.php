{{-- Contact Block --}}
<section class="contact-section py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                @if(isset($data['heading']))
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
                        {{ $data['heading'] }}
                    </h2>
                @endif
                
                @if(isset($data['description']))
                    <p class="text-xl text-gray-600">
                        {{ $data['description'] }}
                    </p>
                @endif
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                {{-- İletişim Formu --}}
                @if(isset($data['show_form']) && $data['show_form'])
                    <div class="bg-gray-50 p-8 rounded-lg">
                        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Adınız</label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Mesajınız</label>
                                <textarea id="message" name="message" rows="4" required
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                Gönder
                            </button>
                        </form>
                    </div>
                @endif
                
                {{-- İletişim Bilgileri --}}
                @if(isset($data['show_info']) && $data['show_info'])
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-gray-900">E-posta</h4>
                                <p class="text-gray-600">{{ \App\Models\Setting::get('contact_email', 'info@example.com') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-gray-900">Telefon</h4>
                                <p class="text-gray-600">{{ \App\Models\Setting::get('contact_phone', '+90 555 123 45 67') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-gray-900">Adres</h4>
                                <p class="text-gray-600">{{ \App\Models\Setting::get('contact_address', 'İstanbul, Türkiye') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Harita --}}
            @if(isset($data['show_map']) && $data['show_map'] && isset($data['map_embed_url']))
                <div class="mt-12">
                    <iframe src="{{ $data['map_embed_url'] }}" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            class="rounded-lg shadow-lg">
                    </iframe>
                </div>
            @endif
        </div>
    </div>
</section>
