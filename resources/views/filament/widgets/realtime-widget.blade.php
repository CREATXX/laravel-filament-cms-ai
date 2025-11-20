<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Gerçek Zamanlı Kullanıcılar --}}
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Şu Anda Sitede</h3>
                    <div class="animate-pulse">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="8"/>
                        </svg>
                    </div>
                </div>
                <div class="text-5xl font-bold mb-2">{{ $this->getRealtimeUsers() }}</div>
                <p class="text-green-100">Aktif kullanıcı</p>
            </div>
            
            {{-- Trafik Kaynakları --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Trafik Kaynakları</h3>
                <div class="space-y-3">
                    @forelse($this->getTrafficSources() as $source)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $source['source'] }}</p>
                                <p class="text-xs text-gray-500">{{ $source['medium'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-blue-600">{{ number_format($source['sessions']) }}</p>
                                <p class="text-xs text-gray-500">oturum</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Veri bulunamadı</p>
                    @endforelse
                </div>
            </div>
            
            {{-- Cihaz Dağılımı --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Cihaz Dağılımı</h3>
                <div class="space-y-3">
                    @forelse($this->getDeviceStats() as $device)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($device['name']) }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $device['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $device['percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($device['users']) }} kullanıcı</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Veri bulunamadı</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
