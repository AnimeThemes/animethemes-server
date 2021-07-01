<x-app-layout>
    <!-- Site  Description -->
    <div class="container mx-auto px-6">
        <h2 class="font-bold text-4xl pt-4 mb-2 text-center text-gray-800">AnimeThemes</h2>
        <div class="font-extralight text-center text-xl">
            A simple and consistent repository of anime opening and ending themes
        </div>
        <div class="font-extralight text-center text-xl">
            We provide high quality WebMs of your favorite OPs and EDs for your listening and discussion needs
        </div>
    </div>

    <!-- Announcements -->
    <div class="container sm:max-w-4xl mx-auto px-6">
        @foreach ($announcements as $announcement)
            <div class="px-4 py-3 m-6 leading-normal text-blue-700 bg-blue-100 rounded-lg">
                <svg class="inline align-middle" xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span class="align-middle">{!! $announcement->content !!}</span>
            </div>
        @endforeach
    </div>

    <!-- Calls to Action -->
    <div class="container sm:max-w-4xl mx-auto px-6">
        <div class="flex flex-row content-center justify-center">
            <div class="w-full sm:max-w-md mx-6 px-6 p-4 space-y-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="font-bold text-lg text-center">{{ __('Browse our repository') }}</div>
                <p>Search the <a class="font-bold hover:underline" href="{{ url('wiki') }}">wiki</a> for listings of themes by Year, Series, Artist and Anime.</p>
                <p>Query the <a class="font-bold hover:underline" href="{{ url('api/docs') }}">API</a> for the scripting needs of your project.</p>
            </div>
            <div class="w-full sm:max-w-md mx-6 px-6 p-4 space-y-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="font-bold text-lg text-center">{{ __('Want to contribute?') }}</div>
                <p>Check out our <a class="font-bold hover:underline" href="{{ url('encoding#guides') }}">encoding guides</a> on how to make WebMs that meet <a class="font-bold hover:underline" href="{{ url('encoding#standards') }}">our standards</a>.</p>
                <p>Contact the moderation team on our <a class="font-bold hover:underline" href="https://discordapp.com/invite/m9zbVyQ">Discord server</a> if you would like to join our wiki contributor team.</p>
            </div>
        </div>
    </div>
</x-app-layout>
