{{-- Image Block --}}
<section class="image-section py-8">
    <div class="container mx-auto px-4">
        <div class="
            @if($data['alignment'] === 'left') text-left
            @elseif($data['alignment'] === 'right') text-right ml-auto
            @elseif($data['alignment'] === 'full') w-full
            @else text-center mx-auto
            @endif
            
            @if($data['size'] === 'small') max-w-md
            @elseif($data['size'] === 'medium') max-w-2xl
            @else max-w-full
            @endif
        ">
            @if(isset($data['image']))
                <img src="{{ asset('storage/' . $data['image']) }}" 
                     alt="{{ $data['caption'] ?? '' }}"
                     class="rounded-lg shadow-lg w-full h-auto">
                
                @if(isset($data['caption']) && $data['caption'])
                    <p class="mt-4 text-gray-600 italic text-center">
                        {{ $data['caption'] }}
                    </p>
                @endif
            @endif
        </div>
    </div>
</section>
