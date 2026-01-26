<x-filament::page>
    <style>
        .mps-badge { box-shadow: 0 1px 0 rgba(0,0,0,0.04); }
        .mps-card-number { font-size: 1.75rem; line-height: 1; }
        .mps-table thead th { background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.85)); }
    </style>

    <div x-data="{ tab: 'parametros' }" class="space-y-6">
        {{-- Cabecera de pestañas --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div>
                    <h2 class="text-lg font-semibold">Propuesta Maestra (MPS)</h2>
                    <div class="text-sm text-gray-500">Planificación de producción y entregas</div>
                </div>

                <div class="flex items-center space-x-2">
                    <button type="button" @click.prevent="tab = 'parametros'" :class="tab === 'parametros' ? 'bg-primary-700 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-3 py-1 rounded-md text-sm">Parámetros</button>
                    <button type="button" @click.prevent="tab = 'resumen'" :class="tab === 'resumen' ? 'bg-primary-700 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-3 py-1 rounded-md text-sm">Resumen</button>
                    <button type="button" @click.prevent="tab = 'tabla'" :class="tab === 'tabla' ? 'bg-primary-700 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-3 py-1 rounded-md text-sm">Tabla</button>
                </div>
            </div>

            <div>
                <x-filament::button color="primary" size="sm" class="px-4 py-2" x-on:click="tab = 'tabla'" wire:click="recalcular">Calcular MPS</x-filament::button>
            </div>
        </div>

        {{-- Contenido de pestañas --}}
        <div>
            {{-- Parámetros (form) --}}
            <div x-show="tab === 'parametros'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <x-filament::section class="col-span-2">
                        <div class="mb-4 text-sm text-gray-600">Ajustá parámetros para generar la propuesta. Los resultados se mostrarán en la pestaña Tabla.</div>
                        {{ $this->form }}
                    </x-filament::section>

                    <div class="col-span-1">
                        <x-filament::card class="p-4">
                            <div class="text-sm text-gray-500">Resumen rápido</div>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">Turnos totales</div>
                                    <div class="mps-card-number font-semibold">{{ $turnosTotales }}</div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">Hornos sugeridos</div>
                                    <div class="mps-card-number font-semibold">{{ $hornosSugeridos }}</div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">Harina (kg)</div>
                                    <div class="mps-card-number font-semibold">{{ number_format($harinaTotalKg,2,',','.') }}</div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">Producción total</div>
                                    <div class="mps-card-number font-semibold">{{ number_format($produccionTotal,2,',','.') }}</div>
                                </div>
                            </div>
                        </x-filament::card>
                    </div>
                </div>
            </div>

            {{-- Resumen --}}
            <div x-show="tab === 'resumen'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <x-filament::card>
                        <div class="text-sm text-gray-500">Hornos sugeridos</div>
                        <div class="text-2xl font-bold">{{ $hornosSugeridos }}</div>
                    </x-filament::card>

                    <x-filament::card>
                        <div class="text-sm text-gray-500">Turnos totales</div>
                        <div class="text-2xl font-bold">{{ $turnosTotales }}</div>
                    </x-filament::card>

                    <x-filament::card>
                        <div class="text-sm text-gray-500">Harina total (kg)</div>
                        <div class="text-2xl font-bold">{{ number_format($harinaTotalKg, 2, ',', '.') }}</div>
                    </x-filament::card>

                    <x-filament::card>
                        <div class="text-sm text-gray-500">Producción total</div>
                        <div class="text-2xl font-bold">{{ number_format($produccionTotal, 2, ',', '.') }}</div>
                    </x-filament::card>
                </div>
            </div>

            {{-- Tabla --}}
            <div x-show="tab === 'tabla'" x-cloak>
                <x-filament::section>
                    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-100 dark:border-gray-800">
                        @php
                            $displayFilas = $soloEntregas
                                ? array_values(array_filter($filas, function($x) { return (!empty($x['entrega_proxima_count']) && $x['entrega_proxima_count'] > 0); }))
                                : $filas;
                        @endphp

                        <table class="min-w-full text-sm mps-table">
                            <thead class="sticky top-0 z-10">
                                <tr class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                                    <th class="text-left p-4">Fecha</th>
                                    <th class="text-right p-4">Inv. Inicial</th>
                                    <th class="text-right p-4">Demanda</th>
                                    <th class="text-right p-4">Sugerida</th>
                                    <th class="text-right p-4">Producción</th>
                                    <th class="text-right p-4">Inv. Final</th>
                                    <th class="text-right p-4">Turnos</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y">
                                @forelse($displayFilas as $f)
                                    @php $isEntrega = !empty($f['entrega_proxima_count']) && $f['entrega_proxima_count'] > 0; @endphp
                                    <tr class="{{ $f['alerta'] ? 'bg-red-50 dark:bg-red-950/30' : ($isEntrega ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800') }}">
                                        <td class="p-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="text-sm font-medium">{{ $f['fecha'] }}</div>
                                                @if($isEntrega)
                                                    <a href="{{ url('/admin/pedidos?fecha=' . $f['fecha'] . '&producto=' . ($productoId ?? '')) }}" class="mps-badge inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-600 text-white">
                                                        Entrega en {{ $diasEntrega }}d: {{ $f['entrega_proxima_count'] }}
                                                    </a>
                                                @endif

                                                @if(!empty($f['ordenes_incluidas_qty']) && $f['ordenes_incluidas_qty'] > 0)
                                                    <a href="{{ url('/admin/orden-produccions?fecha=' . $f['fecha']) }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200 ml-2">
                                                        Órdenes incl.: {{ number_format($f['ordenes_incluidas_qty'], 2, ',', '.') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4 text-right text-gray-600">{{ number_format($f['inv_inicial'], 2, ',', '.') }}</td>
                                        <td class="p-4 text-right text-gray-600">{{ number_format($f['demanda'], 2, ',', '.') }}</td>
                                        <td class="p-4 text-right">
                                            <span class="inline-flex rounded-md px-3 py-1 text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                                                {{ number_format($f['prod_sugerida'], 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-right">
                                            <input type="number" step="0.01" min="0" wire:model.live="produccionEditada.{{ $f['fecha'] }}" class="w-32 text-right rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-2 py-2 text-sm" />
                                        </td>
                                        <td class="p-4 text-right font-semibold">{{ number_format($f['inv_final'], 2, ',', '.') }}</td>
                                        <td class="p-4 text-right">{{ $f['turnos'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-gray-500">Elegí producto + rango y tocá “Calcular MPS”.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if(count($filas))
                                <tfoot class="bg-gray-50 dark:bg-gray-900">
                                    <tr class="border-t font-semibold">
                                        <td class="p-4">Totales</td>
                                        <td></td>
                                        <td class="text-right p-4">{{ number_format(array_sum(array_column($filas, 'demanda')), 2, ',', '.') }}</td>
                                        <td class="text-right p-4">{{ number_format(array_sum(array_column($filas, 'prod_sugerida')), 2, ',', '.') }}</td>
                                        <td class="text-right p-4">{{ number_format($produccionTotal, 2, ',', '.') }}</td>
                                        <td></td>
                                        <td class="text-right p-4">{{ $turnosTotales }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <div class="mt-3 text-xs text-gray-500">Nota: filas en rojo = inventario final por debajo del stock mínimo.</div>
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament::page>
