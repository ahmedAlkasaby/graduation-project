<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Memory Heavy Queries"
        x-bind:title="`Time: {{ number_format($time) }}ms; Run at: ${formatDate('{{ $runAt }}')};`"
        details="> {{ $config['threshold'] }}MB allocated, past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.circle-stack />
        </x-slot:icon>
        <x-slot:actions>
            <x-pulse::select
                wire:model.live="orderBy"
                id="select-memory-heavy-queries-order-by"
                label="Sort by"
                :options="[
                    'heaviest' => 'heaviest',
                    'count' => 'count',
                ]"
                @change="loading = true"
            />
        </x-slot:actions>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($queries->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Query</x-pulse::th>
                        <x-pulse::th class="text-right">Count</x-pulse::th>
                        <x-pulse::th class="text-right">Heaviest</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($queries->take(100) as $query)
                        <tr wire:key="{{ md5($query->sql.$query->location) }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ md5($query->sql.$query->location) }}-row">
                            <x-pulse::td class="!p-0 truncate max-w-[1px]">
                                <div class="relative">
                                    <div class="bg-gray-700 dark:bg-gray-800 py-4 rounded-md text-gray-100 block text-xs whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                        <code class="px-3">{{ $query->sql }}</code>
                                        @if ($query->location)
                                            <p class="px-3 mt-3 text-xs leading-none text-gray-400 dark:text-gray-500">
                                                {{ $query->location }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="absolute top-0 right-0 bottom-0 rounded-r-md w-3 bg-gradient-to-r from-transparent to-gray-700 dark:to-gray-800 pointer-events-none"></div>
                                </div>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                @if ($config['sample_rate'] < 1)
                                    <span title="Sample rate: {{ $config['sample_rate'] }}, Raw value: {{ number_format($query->count) }}">~{{ number_format($query->count * (1 / $config['sample_rate'])) }}</span>
                                @else
                                    {{ number_format($query->count) }}
                                @endif
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                <strong>{{ $query->heaviest }}</strong> MB
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>

            @if ($queries->count() > 100)
                <div class="mt-2 text-xs text-gray-400 text-center">Limited to 100 entries</div>
            @endif
        @endif
    </x-pulse::scroll>
</x-pulse::card>
