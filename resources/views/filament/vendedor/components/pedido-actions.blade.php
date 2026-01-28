<div class="flex items-center justify-center gap-1">
    {{-- Ver detalle --}}
    <a href="{{ $editUrl }}" 
       title="Ver detalle"
       class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition-all duration-200 shadow-sm hover:shadow">
        <x-filament::icon icon="heroicon-o-eye" class="w-4 h-4"/>
    </a>
    
    {{-- Editar --}}
    <a href="{{ $editUrl }}" 
       title="Editar pedido"
       class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-warning-500 hover:bg-warning-600 text-white transition-all duration-200 shadow-sm hover:shadow">
        <x-filament::icon icon="heroicon-o-pencil" class="w-4 h-4"/>
    </a>
    
    {{-- Cancelar (solo si no está cancelado) --}}
    @if($record->estado !== 'cancelado')
    <button type="button"
            onclick="if(confirm('¿Estás seguro de que quieres cancelar este pedido?')) { window.location.href='{{ $editUrl }}'; }"
            title="Cancelar pedido"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-danger-600 hover:bg-danger-700 text-white transition-all duration-200 shadow-sm hover:shadow">
        <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4"/>
    </button>
    @endif
</div>
