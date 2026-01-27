<x-filament::page>
    <style>
        /* MPS table base */
        .mps-badge { box-shadow: 0 1px 0 rgba(0,0,0,0.04); }
        .mps-card-number { font-size: 1.6rem; line-height: 1; }

        .mps-table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 0; font-size: 0.9rem; }
        .mps-table thead th { background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(255,255,255,0.95)); position: sticky; top: 0; z-index: 15; border-bottom: 1px solid #e6e6e6; }
        .mps-table th, .mps-table td { padding: 0.35rem 0.5rem; vertical-align: middle; }

        /* Left-most column (labels) stays visible */
        .mps-table th:first-child, .mps-table td:first-child {
            position: sticky; left: 0; z-index: 30; background: #fff; min-width: 220px; max-width: 260px; text-align: left; font-weight: 700;
            border-right: 1px solid rgba(0,0,0,0.06); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* Date/value columns */
        .mps-table thead th.date-col { text-align: center; font-weight: 700; min-width: 86px; }
        .mps-table tbody td.date-col, .mps-table tbody td.value-col { text-align: right; white-space: nowrap; min-width: 78px; }

        /* Highlight negative inventory */
        .mps-neg { background: #ef4444; color: white; border-radius: 0.25rem; padding: 0.15rem 0.4rem; display: inline-block; }

        /* Alternate banding */
        .mps-table tbody tr:nth-child(odd) td { background: rgba(0,0,0,0.01); }

        .mps-container { background: white; border-radius: 0.375rem; padding: 0.5rem; box-shadow: 0 6px 18px rgba(0,0,0,0.03); }

        /* Responsive: allow horizontal scroll on small screens */
        .mps-scroll { overflow-x: auto; }

        /* Product panel */
        .mps-product-panel { border: 1px solid #e6e6e6; border-radius: .375rem; margin-bottom: .6rem; background: #fff; }
        .mps-product-header { display:flex; align-items:center; gap:.5rem; padding:.45rem .6rem; cursor:pointer; }
        .mps-product-title { font-weight:600; font-size:.94rem; }
        .mps-collapse-icon { width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; transition:transform .18s ease; }

        .mps-empty-placeholder { color:#6b7280; padding:1rem; text-align:center; }

        /* Totals row */
        .mps-table tfoot td { font-weight: 700; border-top: 1px solid #e5e7eb; }

        /* Custom tab buttons */
        .mps-tab-btn {
            margin-right: 18px;
            padding: 0.5rem 1.2rem;
            font-weight: 600;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            border: 1.5px solid #e5e7eb;
            background: #f3f4f6;
            color: #374151;
            transition: all 0.18s;
        }
        .mps-tab-btn:last-child { margin-right: 0; }
        .mps-tab-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
        .mps-tab-btn:hover:not(.active) {
            background: #e0e7ff;
            color: #1e40af;
        }
    </style>

    <div x-data="{ tab: @entangle('openTab'), tabPlan: 'parametros' }" class="space-y-6">
        {{-- Cabecera de solapas --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <button type="button"
                        @click.prevent="tab = 'plan'"
                        :class="['mps-tab-btn', tab === 'plan' ? 'active' : '']"
                    >Plan maestro de producción</button>
                    <button type="button"
                        @click.prevent="tab = 'capacidad'"
                        :class="['mps-tab-btn', tab === 'capacidad' ? 'active' : '']"
                    >Capacidad requerida</button>
                    <button type="button"
                        @click.prevent="tab = 'calendario'"
                        :class="['mps-tab-btn', tab === 'calendario' ? 'active' : '']"
                    >Calendario de stock</button>
                </div>
            </div>

            <div>
                <x-filament::button color="primary" size="sm" class="px-4 py-2" x-on:click="tab = 'plan'" wire:click="recalcular">Calcular MPS</x-filament::button>
            </div>
        </div>

        {{-- Contenido de solapas --}}
        <div>
            {{-- Calendario de stock (vacío) --}}
            <div x-show="tab === 'calendario'" x-cloak></div>

            {{-- Capacidad requerida (vacío) --}}
            <div x-show="tab === 'capacidad'" x-cloak></div>

            {{-- Plan maestro de producción (antes Tabla) --}}
            <div x-show="tab === 'plan'" x-cloak>
                {{-- Subopciones de la solapa Plan maestro de producción --}}
                <div class="flex items-center mb-6">
                    <button type="button"
                        @click.prevent="tabPlan = 'parametros'"
                        :class="['mps-tab-btn', tabPlan === 'parametros' ? 'active' : '']"
                    >Parámetros</button>
                    <button type="button"
                        @click.prevent="tabPlan = 'resumen'"
                        :class="['mps-tab-btn', tabPlan === 'resumen' ? 'active' : '']"
                    >Resumen</button>
                    <button type="button"
                        @click.prevent="tabPlan = 'tabla'"
                        :class="['mps-tab-btn', tabPlan === 'tabla' ? 'active' : '']"
                    >Tabla</button>
                </div>

                {{-- Contenido de subopciones --}}
                <div x-show="tabPlan === 'parametros'" x-cloak>
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
                            <x-filament::card class="mt-4 p-3">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold">Últimos productos calculados</div>
                                    <button wire:click="clearProductosCalculados" class="text-xs text-gray-500 hover:text-gray-700">Limpiar</button>
                                </div>

                                <div class="mt-3">
                                    @if(empty($this->productosCalculados))
                                        <div class="text-sm text-gray-500">No hay productos calculados aún.</div>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($this->productosCalculados as $p)
                                                <li>
                                                    <button wire:click="selectProducto({{ $p['id'] }})" class="w-full text-left text-sm px-2 py-1 rounded hover:bg-gray-50">
                                                        <div class="flex items-center justify-between">
                                                            <div class="text-sm">{{ $p['nombre'] }}</div>
                                                            <div class="text-xs text-gray-400">{{ \Illuminate\Support\Carbon::parse($p['calculado_en'])->format('Y-m-d H:i') }}</div>
                                                        </div>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </x-filament::card>
                        </div>
                    </div>
                </div>

                <div x-show="tabPlan === 'resumen'" x-cloak>
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

                <div x-show="tabPlan === 'tabla'" x-cloak>
                    <x-filament::section>
                    @php
                        $results = $this->productosResultados ?? [];
                    @endphp

                    @if(empty($results))
                        <div class="p-6 text-sm text-gray-600">No hay resultados. Elegí parámetros y pulsá "Calcular MPS".</div>
                    @else
                        @foreach($results as $pid => $r)
                            @php
                                $product = $r['producto'];
                                $displayFilas = $soloEntregas
                                    ? array_values(array_filter($r['filas'], function($x) { return (!empty($x['entrega_proxima_count']) && $x['entrega_proxima_count'] > 0); }))
                                    : $r['filas'];
                                $dates = array_map(fn($rr) => $rr['fecha'], $displayFilas);
                            @endphp

                            <div x-data="{ open_{{ $pid }}: true }" class="mb-4">
                                <div class="mps-product-panel">
                                    <div class="mps-product-header" x-on:click="open_{{ $pid }} = !open_{{ $pid }}">
                                        <div class="mps-collapse-icon" :class="open_{{ $pid }} ? 'rotate-0' : '-rotate-90'">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </div>

                                        <div class="mps-product-title">{{ $product?->nombre ?? 'Producto' }}</div>
                                        <div class="ml-4 text-sm text-gray-500">Turnos: {{ $r['turnosTotales'] }} · Harina: {{ number_format($r['harinaTotalKg'],2,',','.') }}</div>
                                        <div class="ml-auto flex items-center gap-2">
                                            <div class="text-sm text-gray-600">Total: {{ number_format($r['produccionTotal'] ?? 0,2,',','.') }}</div>
                                            <button wire:click="exportProductoCsv({{ $product->id }})" class="text-xs px-2 py-1 bg-gray-100 rounded">CSV</button>
                                        </div>
                                    </div>

                                    @if(empty($displayFilas))
                                        <div class="p-4 text-sm text-gray-600">No hay datos para este producto en el rango seleccionado.</div>
                                    @else
                                        <div x-show="open_{{ $pid }}" class="mps-scroll mps-container">
                                            <table class="min-w-full text-sm mps-table">
                                                <thead class="sticky top-0 z-10 bg-white dark:bg-gray-900">
                                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                                        <th class="p-3 text-left">Productos</th>
                                                        @foreach($dates as $d)
                                                            @php
                                                                $row = current(array_filter($displayFilas, function($x) use ($d) { return $x['fecha'] === $d; }));
                                                            @endphp
                                                            <th class="p-3 text-center date-col">
                                                                <div class="text-sm font-medium">{{ $d }}</div>
                                                                @if(!empty($row['entrega_proxima_count']))
                                                                    <div class="text-xs mt-1 inline-flex items-center px-2 py-0.5 rounded text-white bg-yellow-600">Entrega {{ $row['entrega_proxima_count'] }}</div>
                                                                @endif
                                                                @if(!empty($row['ordenes_incluidas_qty']))
                                                                    <div class="text-xs mt-1 inline-flex items-center px-2 py-0.5 rounded text-green-800 bg-green-100">Incl.: {{ number_format($row['ordenes_incluidas_qty'],0,',','.') }}</div>
                                                                @endif
                                                                <form method="POST" action="{{ route('admin.crear-orden-agrupada') }}" style="display:inline">
                                                                    @csrf
                                                                    <input type="hidden" name="producto_id" value="{{ $product->id }}">
                                                                    <input type="hidden" name="fecha" value="{{ $d }}">
                                                                    <button type="submit" class="text-xs mt-1 inline-flex items-center px-2 py-0.5 rounded text-white bg-blue-600 hover:bg-blue-700">Crear orden agrupada</button>
                                                                </form>
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>

                                                <tbody class="bg-white dark:bg-gray-900 divide-y">
                                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                                        <td class="p-3 font-semibold" title="Cantidad de producto disponible al inicio del periodo">Inventario inicial</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col text-gray-600">{{ number_format($f['inv_inicial'] ?? 0, 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>

                                                    <tr>
                                                        <td class="p-3 font-semibold text-red-600" title="Cantidad máxima que se espera vender o entregar en el periodo">Demanda (máx. de)</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col text-red-600 font-semibold">{{ number_format($f['demanda'] ?? 0, 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>

                                                    <tr>
                                                        <td class="p-3 pl-6 text-sm text-gray-600" title="Estimación de ventas basada en pronósticos">Pronóstico de ventas</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col text-gray-600">{{ number_format($f['pronostico'] ?? $f['demanda'] ?? 0, 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>

                                                    <tr>
                                                        <td class="p-3 pl-6 text-sm text-gray-600" title="Órdenes confirmadas que deben cumplirse">Órdenes en firme</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col text-gray-600">{{ number_format($f['ordenes_en_firme'] ?? $f['ordenes_incluidas_qty'] ?? 0, 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>

                                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                                        <td class="p-3 font-semibold" title="Cantidad máxima que se puede suministrar en el periodo">Suministro (máx. de)</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col text-gray-600">{{ number_format($f['suministro_max'] ?? ($f['prod_sugerida'] ?? 0), 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>

                                                    <tr>
                                                        <td class="p-3 pl-6 text-sm text-gray-600" title="Cantidad sugerida para producir en el periodo">Plan de producción</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col">
                                                                <div class="inline-flex items-center justify-end">
                                                                    <span class="mr-2 inline-block text-xs text-gray-500 hidden md:inline">Sugerida</span>
                                                                    <span class="inline-flex rounded-md px-3 py-1 text-sm font-medium bg-blue-50 text-blue-700">{{ number_format($f['prod_sugerida'] ?? 0, 2, ',', '.') }}</span>
                                                                </div>
                                                            </td>
                                                        @endforeach
                                                    </tr>

                                                    <tr>
                                                        <td class="p-3 pl-6 text-sm text-gray-600" title="Cantidad ya programada para producción">Ya programado</td>
                                                        @foreach($displayFilas as $f)
                                                            <td class="p-3 text-right value-col text-gray-600">{{ number_format($f['ya_programado'] ?? 0, 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>

                                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                                        <td class="p-3 font-semibold" title="Cantidad de producto que queda al final del periodo">Inventario final</td>
                                                        @foreach($displayFilas as $f)
                                                            @php $inv = $f['inv_final'] ?? 0; @endphp
                                                            <td class="p-3 text-right value-col font-semibold {{ $inv < 0 ? 'bg-red-600 text-white' : '' }}">{{ number_format($inv, 2, ',', '.') }}</td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>

                                                @if(count($displayFilas))
                                                    <tfoot class="bg-gray-50 dark:bg-gray-900">
                                                        <tr class="border-t font-semibold">
                                                            <td class="p-3">Totales</td>
                                                            @foreach($displayFilas as $f)
                                                                <td class="p-3 text-right">&nbsp;</td>
                                                            @endforeach
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            @endforeach
                    @endif

                    <div class="mt-3 text-xs text-gray-500">Nota: filas en rojo = inventario final por debajo del stock mínimo. Usa la fila "Plan de producción" para ver la propuesta sugerida por semana.</div>
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament::page>
