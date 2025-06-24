<div class="flex rounded-md relative">
    <div class="flex">
        <div class="flex flex-col justify-center pl-3 py-2">
            <p class="text-sm font-bold pb-1">{{ $name }}</p>
            @if ($subtitle !== null)
            <div class="flex flex-col items-start">
                <p class="text-xs leading-5">{{ $subtitle }}</p>
            </div>
            @endif
        </div>
    </div>
</div>