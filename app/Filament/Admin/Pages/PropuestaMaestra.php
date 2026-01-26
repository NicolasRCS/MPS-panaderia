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
    protected static ?string $navigationLabel = 'Propuesta Maestra (MPS)';
    protected static ?string $title = 'Propuesta Maestra (MPS)';

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

    /** Resúmenes */
    public float $harinaTotalKg = 0.0;
    public int $turnosTotales = 0;
    public int $hornosSugeridos = 0;
    public float $produccionTotal = 0.0;

    public function getView(): string
    {
        return 'filament.pages.propuesta-maestra';
    }

    public function mount(): void
    {
        $this->desde = now()->toDateString();
        $this->hasta = now()->addDays(6)->toDateString();
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
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required()
                ->live(),

            Select::make('ordenesSeleccionadas')
                ->label('Incluir Órdenes de producción')
                ->options(fn () => OrdenProduccion::with(['producto', 'pedido'])->get()->mapWithKeys(fn($o) => [ $o->id => ($o->producto?->nombre ?? '—') . ' — ' . (float)$o->cantidad . ' (' . ($o->pedido?->fecha ?? '-') . ')' ])->toArray())
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
        $this->harinaTotalKg = 0.0;
        $this->turnosTotales = 0;
        $this->hornosSugeridos = 0;

        if (!$this->productoId || !$this->desde || !$this->hasta) {
            return;
        }

        $producto = Producto::find($this->productoId);
        if (!$producto) return;

        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->startOfDay();
        if ($hasta->lt($desde)) return;

        /** @var Collection<string, \Illuminate\Support\Collection> $pedidos */
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

        // Pre-scheduled production from selected Ordenes de Producción (grouped by pedido fecha)
        $preScheduledPerDate = [];
        if (!empty($this->ordenesSeleccionadas)) {
            $ordenes = OrdenProduccion::with('pedido')->whereIn('id', $this->ordenesSeleccionadas)->get();
            foreach ($ordenes as $o) {
                $fechaPedido = $o->pedido?->fecha ?? null;
                if ($fechaPedido) {
                    $preScheduledPerDate[$fechaPedido] = ($preScheduledPerDate[$fechaPedido] ?? 0) + (float) $o->cantidad;
                }
            }
        }

        foreach (CarbonPeriod::create($desde, $hasta) as $dia) {
            $fecha = $dia->toDateString();

            // ✅ FIX: usar get() para no romper si no hay pedidos ese día
            $demanda = (float) ($pedidos->get($fecha)?->sum('cantidad') ?? 0);

            $invInicial = $stock;
            $invLuegoDemanda = $invInicial - $demanda;

            // Sugerencia (neteado + lote)
            $produccionSugerida = 0.0;

            if ($invLuegoDemanda < $minStock) {
                $necesidad = $minStock - $invLuegoDemanda;

                $produccionSugerida = $lote > 0
                    ? ceil($necesidad / $lote) * $lote
                    : $necesidad;
            }

            // Restar producción ya planificada (ordenes seleccionadas) para esta fecha
            $preScheduled = $preScheduledPerDate[$fecha] ?? 0.0;
            if ($preScheduled > 0) {
                $produccionSugerida = max(0.0, $produccionSugerida - $preScheduled);
            }

            // Producción editable (toque humano)
            $produccion = array_key_exists($fecha, $this->produccionEditada)
                ? (float) $this->produccionEditada[$fecha]
                : $produccionSugerida;

            $this->produccionEditada[$fecha] = $produccion;

            $invFinal = $invLuegoDemanda + $produccion;

            // Turnos
            $turnos = ($capTurno > 0 && $produccion > 0)
                ? (int) ceil($produccion / $capTurno)
                : 0;

            $this->turnosTotales += $turnos;
            $produccionTotal += $produccion + ($preScheduledPerDate[$fecha] ?? 0.0);

            $entregaProximaCount = 0;
            $targetDate = now()->copy()->addDays($this->diasEntrega)->toDateString();
            if ($fecha === $targetDate) {
                $entregaProximaCount = $pedidos->get($fecha)?->count() ?? 0;
            }

            $this->filas[] = [
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
                'ordenes_incluidas_ids' => array_values(array_filter($this->ordenesSeleccionadas, fn($id) => true)),
            ];

            $stock = $invFinal;
        }

        // Hornos sugeridos (simple)
        $tPorH = max(1, (int) $this->turnosPorHornoPorDia);
        $this->hornosSugeridos = (int) ceil($this->turnosTotales / $tPorH);

        // Harina total (ingrediente que contenga "harina")
        $harinaReceta = Receta::query()
            ->where('producto_id', $producto->id)
            ->whereHas('ingrediente', fn ($q) => $q->whereRaw('LOWER(nombre) LIKE ?', ['%harina%']))
            ->first();

        $this->harinaTotalKg = $harinaReceta
            ? round($produccionTotal * (float) $harinaReceta->cantidad, 2)
            : 0.0;

        $this->produccionTotal = round($produccionTotal, 2);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
