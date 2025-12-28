<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <div class="bg-dark rounded-t shadow px-5 py-2">
        <h1 class="text-[16px] text-white">{!! $title !!}</h1>
    </div>
    <div class="bg-white rounded-b shadow p-5">
        {{ $slot }}
    </div>
</div>
