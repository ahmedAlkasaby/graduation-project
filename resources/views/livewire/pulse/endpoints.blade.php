<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Endpoints"
        x-bind:title="`Time: {{ number_format($time) }}ms; Run at: ${formatDate('{{ $runAt }}')};`"
        details="requests, past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.arrows-left-right />
        </x-slot:icon>
        <x-slot:actions>
            <x-pulse::select
                wire:model.live="orderBy"
                id="select-endpoints-order-by"
                label="Sort by"
                :options="[
                    'count' => 'count',
                    'avg' => 'average',
                    'max' => 'slowest',
                ]"
                @change="loading = true"
            />
        </x-slot:actions>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($endpoints->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="0%" />
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Method</x-pulse::th>
                        <x-pulse::th>Route</x-pulse::th>
                        <x-pulse::th class="text-right">Count</x-pulse::th>
                        <x-pulse::th class="text-right">Average</x-pulse::th>
                        <x-pulse::th class="text-right">Slowest</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($endpoints->take(100) as $endpoint)
                        <tr wire:key="{{ $endpoint->method.$endpoint->uri }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $endpoint->method.$endpoint->uri }}-row">
                            <x-pulse::td>
                                <x-pulse::http-method-badge :method="$endpoint->method" />
                            </x-pulse::td>
                            <x-pulse::td class="overflow-hidden max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $endpoint->uri }}">
                                    {{ $endpoint->uri }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                @if ($config['sample_rate'] < 1)
                                    <span title="Sample rate: {{ $config['sample_rate'] }}, Raw value: {{ number_format($endpoint->count) }}">~{{ number_format($endpoint->count * (1 / $config['sample_rate'])) }}</span>
                                @else
                                    {{ number_format($endpoint->count) }}
                                @endif
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300">
                                @if ($endpoint->average === null)
                                    <strong>Unknown</strong>
                                @else
                                    <strong>{{ number_format($endpoint->average) ?: '<1' }}</strong> ms
                                @endif
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300">
                                @if ($endpoint->slowest === null)
                                    <strong>Unknown</strong>
                                @else
                                    <strong>{{ number_format($endpoint->slowest) ?: '<1' }}</strong> ms
                                @endif
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>

            @if ($endpoints->count() > 100)
                <div class="mt-2 text-xs text-gray-400 text-center">Limited to 100 entries</div>
            @endif
        @endif
    </x-pulse::scroll>
</x-pulse::card>
