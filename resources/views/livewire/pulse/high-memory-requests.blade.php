<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="High Memory Requests"
        x-bind:title="`Time: {{ number_format($time) }}ms; Run at: ${formatDate('{{ $runAt }}')};`"
        details="> {{ $config['threshold'] }}MB, past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.server />
        </x-slot:icon>
        <x-slot:actions>
            <x-pulse::select
                wire:model.live="orderBy"
                id="select-high-memory-order-by"
                label="Sort by"
                :options="[
                    'max' => 'highest',
                    'count' => 'count',
                ]"
                @change="loading = true"
            />
        </x-slot:actions>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($entries->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="0%" />
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Method</x-pulse::th>
                        <x-pulse::th>Route</x-pulse::th>
                        <x-pulse::th class="text-right">Count</x-pulse::th>
                        <x-pulse::th class="text-right">Peak</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($entries->take(100) as $entry)
                        <tr wire:key="{{ $entry->method.$entry->path }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $entry->method.$entry->path }}-row">
                            <x-pulse::td>
                                <x-pulse::http-method-badge :method="$entry->method" />
                            </x-pulse::td>
                            <x-pulse::td class="overflow-hidden max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $entry->path }}">
                                    {{ $entry->path }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                @if ($config['sample_rate'] < 1)
                                    <span title="Sample rate: {{ $config['sample_rate'] }}, Raw value: {{ number_format($entry->count) }}">~{{ number_format($entry->count * (1 / $config['sample_rate'])) }}</span>
                                @else
                                    {{ number_format($entry->count) }}
                                @endif
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                <strong>{{ $entry->peak }}</strong> MB
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>

            @if ($entries->count() > 100)
                <div class="mt-2 text-xs text-gray-400 text-center">Limited to 100 entries</div>
            @endif
        @endif
    </x-pulse::scroll>
</x-pulse::card>
