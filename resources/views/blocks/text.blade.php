{{-- Text Block --}}
<section class="text-section py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            @if(isset($data['heading']))
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-900">
                    {{ $data['heading'] }}
                </h2>
            @endif
            
            @if(isset($data['content']))
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! $data['content'] !!}
                </div>
            @endif
        </div>
    </div>
</section>
