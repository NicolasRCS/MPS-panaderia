<?php

namespace App\Filament\Admin\Pages;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Horno;
use App\Models\OrdenProduccion;
use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Concerns\InteractsWithForms;

class PropuestaMaestra extends Page
{
    use InteractsWithForms;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Plan Maestro de producción';
    protected static ?string $title = 'Plan Maestro de producción';

    // Datos del form
    public ?int $productoId = null;
    public ?string $desde = null;
    public ?string $hasta = null;
    public int $turnosPorHornoPorDia = 1;
    public ?int $hornoId = null;
    public int $diasEntrega = 3;
    public bool $soloEntregas = false;
    public array $ordenesSeleccionadas = [];

    /** Producción editable por día: ['YYYY-MM-DD' => 50, ...] */
    public array $produccionEditada = [];

    /** Resultado calculado para la tabla */
    public array $filas = [];

    /** Resultados por producto (cuando se calculan varios productos) */
    public array $productosResultados = [];

    // Control de pestañas en cliente (Alpine) enlazado con Livewire
    public string $openTab = 'parametros';

    /** Lista de productos calculados (historial) */
    public array $productosCalculados = [];

    // Máximo de productos a mantener en el historial
    protected int $maxProductosHistorial = 10;

    /** Resúmenes */
    public float $harinaTotalKg = 0.0;
    public int $turnosTotales = 0;
    public int $hornosSugeridos = 0;
    public float $produccionTotal = 0.0;

    public function getView(): string
    {
        return 'filament.admin.propuesta-maestra';
    }

    public function mount(): void
    {
        $this->desde = now()->toDateString();
        $this->hasta = now()->addDays(6)->toDateString();
        // Cargar historial de productos calculados desde la sesión
        $this->productosCalculados = session()->get('mps_productos_calculados', []);
    }

    /** Form estilo Filament */
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('hornoId')
                ->label('Horno (opcional)')
                ->options(Horno::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->placeholder('Usar cualquiera')
                ->searchable()
                ->live(),

            Select::make('productoId')
                ->label('Producto (opcional)')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->helperText('Dejar vacío para calcular MPS de todos los productos')
                ->live(),

            Select::make('ordenesSeleccionadas')
                ->label('Incluir Órdenes de producción')
                ->options(fn () => OrdenProduccion::with(['producto', 'pedidos'])->get()->mapWithKeys(fn($o) => [
                    $o->id => ($o->producto?->nombre ?? '—') . ' — ' . (float)$o->cantidad . ' (' . ($o->pedidos->pluck('fecha')->implode(', ') ?: '-') . ')'
                ])->toArray())
                ->multiple()
                ->searchable()
                ->helperText('Seleccioná órdenes que ya están planificadas y querés que formen parte de la Propuesta Maestra')
                ->live(),

            DatePicker::make('desde')
                ->label('Desde')
                ->required()
                ->live(),

            DatePicker::make('hasta')
                ->label('Hasta')
                ->required()
                ->live(),
            TextInput::make('diasEntrega')
                ->label('Destacar pedidos en (días)')
                ->numeric()
                ->minValue(0)
                ->default(3)
                ->helperText('Ej: 3 = mostrar pedidos cuya fecha sea hoy + 3 días')
                ->live(),
            Toggle::make('soloEntregas')
                ->label('Solo entregas próximas')
                ->helperText('Mostrar únicamente las filas con pedidos cuya fecha coincide con hoy + N días')
                ->default(false)
                ->live(),
            TextInput::make('turnosPorHornoPorDia')
                ->label('Turnos por horno / día')
                ->numeric()
                ->minValue(1)
                ->required()
                ->live(),
        ])->columns(4);
    }

    public function updated($name, $value): void
    {
        // Recalcular cuando cambie algo relevante
        if (in_array($name, ['productoId', 'desde', 'hasta', 'turnosPorHornoPorDia', 'diasEntrega', 'ordenesSeleccionadas', 'soloEntregas'], true)) {
            $this->recalcular();
        }
    }

    public function recalcular(): void
    {
        Log::info('PropuestaMaestra::recalcular called', ['productoId' => $this->productoId, 'desde' => $this->desde, 'hasta' => $this->hasta]);
        $this->filas = [];
        $this->productosResultados = [];
        $this->harinaTotalKg = 0.0;
        $this->turnosTotales = 0;
        $this->hornosSugeridos = 0;

        if (!$this->desde || !$this->hasta) {
            return;
        }

        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->startOfDay();
        if ($hasta->lt($desde)) return;

        // Decide qué productos calcular: uno (si se seleccionó) o todos
        $productosQuery = $this->productoId ? Producto::where('id', $this->productoId) : Producto::query();
        $productos = $productosQuery->orderBy('nombre')->get();

        $globalProduccionTotal = 0.0;
        $globalTurnos = 0;
        $globalHarina = 0.0;

        foreach ($productos as $producto) {
            // Pedidos agrupados por fecha para este producto
            $pedidos = Pedido::query()
                ->where('producto_id', $producto->id)
                ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
                ->get()
                ->groupBy(fn ($p) => Carbon::parse($p->fecha)->toDateString());

            $stock = (float) $producto->stock_inicial;
            $minStock = (float) $producto->stock_minimo;
            $lote = max(0.0, (float) $producto->tamano_lote);
            $capTurno = max(0.0, (float) $producto->capacidad_por_turno);

            $produccionTotal = 0.0;
            $turnosTotalesLocal = 0;

            // Agrupar pedidos por producto y fecha para crear una sola orden de producción por grupo
            $preScheduledPerDate = [];
            if (!empty($this->ordenesSeleccionadas)) {
                $ordenes = OrdenProduccion::with('pedidos')->whereIn('id', $this->ordenesSeleccionadas)->get();
                foreach ($ordenes as $o) {
                    // Tomar la fecha de los pedidos asociados (puede haber varios)
                    foreach ($o->pedidos as $pedido) {
                        $fechaPedido = $pedido->fecha ?? null;
                        if ($fechaPedido) {
                            $preScheduledPerDate[$fechaPedido] = ($preScheduledPerDate[$fechaPedido] ?? 0) + (float) $o->cantidad;
                        }
                    }
                }
            }

            $filasLocal = [];

            foreach (CarbonPeriod::create($desde, $hasta) as $dia) {
                $fecha = $dia->toDateString();
                    $demanda = (float) ($pedidos->get($fecha)?->sum('cantidad') ?? 0);

                    // Órdenes en firme (pedidos confirmados) para esta fecha
                    $ordenesEnFirme = (float) \App\Models\Pedido::where('producto_id', $producto->id)
                        ->whereDate('fecha', $fecha)
                        ->where('estado', 'firme')
                        ->sum('cantidad');

                $invInicial = $stock;
                $invLuegoDemanda = $invInicial - $demanda;

                $produccionSugerida = 0.0;
                if ($invLuegoDemanda < $minStock) {
                    $necesidad = $minStock - $invLuegoDemanda;
                    $produccionSugerida = $lote > 0 ? ceil($necesidad / $lote) * $lote : $necesidad;
                }

                $preScheduled = $preScheduledPerDate[$fecha] ?? 0.0;
                if ($preScheduled > 0) {
                    $produccionSugerida = max(0.0, $produccionSugerida - $preScheduled);
                }

                $produccion = array_key_exists($fecha, $this->produccionEditada) ? (float) $this->produccionEditada[$fecha] : $produccionSugerida;
                $this->produccionEditada[$fecha] = $produccion;

                $invFinal = $invLuegoDemanda + $produccion;

                $turnos = ($capTurno > 0 && $produccion > 0) ? (int) ceil($produccion / $capTurno) : 0;
                $turnosTotalesLocal += $turnos;
                $produccionTotal += $produccion + ($preScheduledPerDate[$fecha] ?? 0.0);

                $entregaProximaCount = 0;
                $targetDate = now()->copy()->addDays($this->diasEntrega)->toDateString();
                if ($fecha === $targetDate) {
                    $entregaProximaCount = $pedidos->get($fecha)?->count() ?? 0;
                }

                $filasLocal[] = [
                    'fecha' => $fecha,
                    'inv_inicial' => $invInicial,
                    'demanda' => $demanda,
                    'prod_sugerida' => $produccionSugerida,
                    'prod' => $produccion,
                    'inv_final' => $invFinal,
                    'turnos' => $turnos,
                    'alerta' => $invFinal < $minStock,
                    'entrega_proxima_count' => $entregaProximaCount,
                    'ordenes_incluidas_qty' => $preScheduledPerDate[$fecha] ?? 0.0,
                    'ordenes_en_firme' => $ordenesEnFirme,
                ];

                $stock = $invFinal;
            }

            // Harina total
            $harinaReceta = Receta::query()
                ->where('producto_id', $producto->id)
                ->whereHas('ingrediente', fn ($q) => $q->whereRaw('LOWER(nombre) LIKE ?', ['%harina%']))
                ->first();

            $harinaKg = $harinaReceta ? round($produccionTotal * (float) $harinaReceta->cantidad, 2) : 0.0;

            $this->productosResultados[$producto->id] = [
                'producto' => $producto,
                'filas' => $filasLocal,
                'produccionTotal' => round($produccionTotal, 2),
                'turnosTotales' => $turnosTotalesLocal,
                'harinaTotalKg' => $harinaKg,
            ];

            // Añadir al historial de productos calculados (session + propiedad)
            $this->addProductoCalculado($producto, round($produccionTotal,2));

            $globalProduccionTotal += $produccionTotal;
            $globalTurnos += $turnosTotalesLocal;
            $globalHarina += $harinaKg;
        }

        $this->produccionTotal = round($globalProduccionTotal, 2);
        $this->turnosTotales = $globalTurnos;
        $this->harinaTotalKg = round($globalHarina, 2);

        // Hornos sugeridos global
        $tPorH = max(1, (int) $this->turnosPorHornoPorDia);
        $this->hornosSugeridos = (int) ceil($this->turnosTotales / $tPorH);

        // Default openTab to 'tabla' so client switches
        $this->openTab = 'tabla';

        // After recalculation, ask client to open the Tabla tab via entangled property
        $this->openTab = 'tabla';
    }

    /**
     * Añade un producto al historial en memoria y en sesión (sin duplicados).
     */
    

    /** Overloaded: añade con producción total opcional y recorta historial */
    protected function addProductoCalculado(Producto $producto, float $produccionTotal = 0.0): void
    {
        $existsIndex = null;
        foreach ($this->productosCalculados as $i => $row) {
            if (($row['id'] ?? null) === $producto->id) {
                $existsIndex = $i;
                break;
            }
        }

        $entry = [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'produccion_total' => $produccionTotal,
            'calculado_en' => now()->toDateTimeString(),
        ];

        // si ya existía, mover al tope y actualizar datos
        if (!is_null($existsIndex)) {
            array_splice($this->productosCalculados, $existsIndex, 1);
        }

        array_unshift($this->productosCalculados, $entry);

        // recortar historial a los últimos N
        if (count($this->productosCalculados) > $this->maxProductosHistorial) {
            $this->productosCalculados = array_slice($this->productosCalculados, 0, $this->maxProductosHistorial);
        }

        session()->put('mps_productos_calculados', $this->productosCalculados);
    }

    /**
     * Selecciona un producto desde la lista de calculados y recalcula.
     */
    public function selectProducto(int $productoId): void
    {
        $this->productoId = $productoId;
        $this->recalcular();
    }

    /**
     * Limpia el historial de productos calculados (sesión + propiedad).
     */
    public function clearProductosCalculados(): void
    {
        $this->productosCalculados = [];
        session()->forget('mps_productos_calculados');
    }

    /** Exporta las filas de un producto en CSV y fuerza descarga */
    public function exportProductoCsv(int $productoId)
    {
        $result = $this->productosResultados[$productoId] ?? null;
        if (!$result) {
            session()->flash('danger', 'No hay datos para ese producto.');
            return null;
        }

        $filename = 'mps_producto_' . $productoId . '_' . now()->format('Ymd_His') . '.csv';

        $rows = [];
        $headers = ['fecha','inv_inicial','demanda','prod_sugerida','prod','ya_programado','ordenes_en_firme','ordenes_incluidas_qty','inv_final','turnos'];
        $rows[] = $headers;
        foreach ($result['filas'] as $f) {
            $rows[] = [
                $f['fecha'] ?? '',
                $f['inv_inicial'] ?? 0,
                $f['demanda'] ?? 0,
                $f['prod_sugerida'] ?? 0,
                $f['prod'] ?? 0,
                $f['ya_programado'] ?? 0,
                $f['ordenes_en_firme'] ?? 0,
                $f['ordenes_incluidas_qty'] ?? 0,
                $f['inv_final'] ?? 0,
                $f['turnos'] ?? 0,
            ];
        }

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
