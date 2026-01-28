<x-filament-widgets::widget class="fi-wi-table pedido-table-scope">
    <style>
        .pedido-table-scope .fi-ta-header-toolbar { display: none; }
        .pedido-table-scope .fi-ta-table { width: 100%; }
    </style>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="mb-4">
            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Listado de Pedidos</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestiona todos los pedidos de producción</p>
        </div>

        <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 mb-4">
            <a href="{{ \App\Filament\Vendedor\Resources\PedidoResource::getUrl('create') }}" 
               class="fi-btn fi-btn-color-primary fi-btn-size-md inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm ring-1 ring-inset transition duration-75 hover:shadow-md">
                <x-filament::icon icon="heroicon-o-plus" class="fi-btn-icon h-5 w-5"/>
                <span class="fi-btn-label">Crear Nuevo Pedido de Producción</span>
            </a>

            <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-3 lg:grid-cols-[minmax(220px,1fr)_180px_180px]">
                <div class="relative">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-gray-400"/>
                    <input
                        type="text"
                        placeholder="Buscar Pedido"
                        wire:model.debounce.500ms="tableSearch"
                        class="w-full rounded-lg border border-gray-300 bg-white px-10 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <select
                    wire:model="tableFilters.rango_fecha.value"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
                    <option value="">Todas las fechas</option>
                    <option value="hoy">Hoy</option>
                    <option value="semana">Esta semana</option>
                    <option value="mes">Este mes</option>
                </select>

                <select
                    wire:model="tableFilters.estado.value"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
                    <option value="">Todos los estados</option>
                    <option value="nuevo">Nuevo</option>
                    <option value="en_produccion">En producción</option>
                    <option value="listo">Listo</option>
                    <option value="finalizado">Finalizado</option>
                    <option value="entregado_al_cliente">Entregado al cliente</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            {{ $this->table }}
        </div>
    </div>
</x-filament-widgets::widget>
