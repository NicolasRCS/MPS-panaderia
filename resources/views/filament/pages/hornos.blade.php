<x-filament::page>
    <div class="space-y-6">
        <x-filament::section>
            <h2 class="text-lg font-semibold">Agregar Horno</h2>
            <div class="mt-3">
                {{ $this->form }}
            </div>
            <div class="mt-4">
                <x-filament::button wire:click="storeHorno">Guardar Horno</x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <h2 class="text-lg font-semibold">Hornos existentes</h2>
            <div class="mt-3">
                <ul class="space-y-2">
                    @foreach(\App\Models\Horno::orderBy('id')->get() as $h)
                        <li class="flex items-center justify-between p-2 bg-gray-800 rounded">
                            <div>
                                <div class="font-medium">{{ $h->nombre }}</div>
                                <div class="text-xs text-gray-400">{{ $h->tipo ?: '—' }}</div>
                            </div>
                            <div class="text-sm">Capacidad: {{ number_format($h->capacidad_por_turno, 2, ',', '.') }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament::page>
