<div class="flex rounded-md relative">
    <div class="flex">
        @if ($image !== null)
        <div class="px-2 py-3">
            <div class="h-10 w-10">
                <img src="{{ $image }}" role="img" class="h-full w-full rounded-full overflow-hidden shadow object-cover" />
            </div>
        </div>
        @endif
        <div class="flex flex-col justify-center pl-3 py-2">
            <p class="text-sm font-bold pb-1">{{ $name }}</p>
            <div class="flex flex-col items-start">
                <p class="text-xs leading-5">{{ $subname }}</p>
            </div>
        </div>
    </div>
</div>