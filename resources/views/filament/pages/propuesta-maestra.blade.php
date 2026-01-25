<x-filament::page>
    <div class="space-y-6">
        {{-- Form Filament (se ve bien en dark) --}}
        <x-filament::section>
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button wire:click="recalcular">
                    Calcular MPS
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Resumen --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section>
                <div class="text-sm text-gray-500">Hornos sugeridos</div>
                <div class="text-2xl font-bold">{{ $hornosSugeridos }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Turnos totales</div>
                <div class="text-2xl font-bold">{{ $turnosTotales }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Harina total (kg)</div>
                <div class="text-2xl font-bold">{{ number_format($harinaTotalKg, 2, ',', '.') }}</div>
            </x-filament::section>
        </div>

        {{-- Tabla estilo Filament (HTML normal) --}}
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="text-left p-3">Fecha</th>
                            <th class="text-right p-3">Inv. Inicial</th>
                            <th class="text-right p-3">Demanda</th>
                            <th class="text-right p-3">Sugerida</th>
                            <th class="text-right p-3">Producción (editable)</th>
                            <th class="text-right p-3">Inv. Final</th>
                            <th class="text-right p-3">Turnos</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($filas as $f)
                            <tr class="border-b border-gray-100 dark:border-gray-900 {{ $f['alerta'] ? 'bg-red-50 dark:bg-red-950/30' : '' }}">
                                <td class="p-3 whitespace-nowrap">{{ $f['fecha'] }}</td>
                                <td class="p-3 text-right">{{ number_format($f['inv_inicial'], 2, ',', '.') }}</td>
                                <td class="p-3 text-right">{{ number_format($f['demanda'], 2, ',', '.') }}</td>
                                <td class="p-3 text-right">
                                    <span class="inline-flex rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                                        {{ number_format($f['prod_sugerida'], 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        wire:model.live="produccionEditada.{{ $f['fecha'] }}"
                                        class="w-28 text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-2 py-1"
                                    />
                                </td>
                                <td class="p-3 text-right font-medium">{{ number_format($f['inv_final'], 2, ',', '.') }}</td>
                                <td class="p-3 text-right">{{ $f['turnos'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">
                                    Elegí producto + rango y tocá “Calcular MPS”.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-xs text-gray-500">
                Nota: filas en rojo = inventario final por debajo del stock mínimo.
            </div>
        </x-filament::section>
    </div>
</x-filament::page>
