<x-app-layout>
    <div class="container mx-auto px-4 sm:px-8">

        <!-- Heading -->
        <div class="py-4">
            <div class="prose">
                <h1 class="text-4xl font-medium">{{ __('Transparency') }}</h1>
            </div>
        </div>

        <!-- Filters -->
        <div class="py-4">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">{{ __('Select Month') }}</h2>
            </div>
            <x-jet-input-error for="date" class="mt-2" />
            <form method="GET" action="{{ route('transparency.show') }}">
                <div class="my-2 flex sm:flex-row flex-col">
                    <div class="flex flex-row mb-1 sm:mb-0">
                        <div class="relative">
                            <select name="date" class="h-full rounded-l">
                                @foreach ($filterOptions as $filterOption)
                                <option {{ $filterOption->equalTo($selectedDate) ? 'selected' : '' }}>{{ $filterOption->format('Y-m') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="block relative">
                        <x-jet-button class="rounded-l-none h-full px-8">
                            {{ __('Go!') }}
                        </x-jet-button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Balances -->
        <div class="py-4">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">{{ __('Balances') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('A listing of account balances against usage or upcoming charges for the selected month. All amounts are expressed in USD.') }}</p>
            </div>

            <div class="flex flex-col">
                <div class="my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Service') }}
                                        </th>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Frequency') }}
                                        </th>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Month to Date Usage') }}
                                        </th>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Balance') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if ($balances->isEmpty())
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No Results Found') }}
                                        </td>
                                    </tr>
                                    @endif
                                    @foreach ($balances as $balance)
                                    <tr class="whitespace-nowrap text-sm text-gray-500 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4">
                                            {{ $balance->service->description }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $balance->frequency->description }}
                                        </td>
                                        <td class="px-6 py-4 font-bold {{ $balance->usage > 0 ? 'text-red-500' : '' }} {{ $balance->usage < 0 ? 'text-green-500' : '' }}">
                                            {{ $balance->usage }}
                                        </td>
                                        <td class="px-6 py-4 font-bold {{ $balance->balance < 0 ? 'text-red-500' : '' }} {{ $balance->balance > 0 ? 'text-green-500' : '' }}">
                                            {{ $balance->balance }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="py-4">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">{{ __('Transactions') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('A listing of invoices and payments for the selected month. All amounts are expressed in USD.') }}</p>
            </div>

            <div class="flex flex-col">
                <div class="my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Service') }}
                                        </th>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Amount') }}
                                        </th>
                                        <th scope="col" class="w-1/4 px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            {{ __('Description') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if ($transactions->isEmpty())
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No Results Found') }}
                                        </td>
                                    </tr>
                                    @endif
                                    @foreach ($transactions as $transaction)
                                    <tr class="whitespace-nowrap text-sm text-gray-500 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4">
                                            {{ $transaction->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->service->description }}
                                        </td>
                                        <td class="px-6 py-4 font-bold {{ $balance->usage >= 0 ? 'text-red-500' : 'text-green-500' }}">
                                            {{ $transaction->amount }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->description }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
