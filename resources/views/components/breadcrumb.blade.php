@props(['items' => []])

<nav class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-3">
        <ol class="flex items-center space-x-2 text-sm">
            @foreach($items as $index => $item)
                @if($index > 0)
                    <li class="text-gray-400">/</li>
                @endif
                
                <li>
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="text-gray-600 hover:text-blue-600 transition">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
