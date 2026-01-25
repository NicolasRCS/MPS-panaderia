<?php

namespace App\Filament\Pages;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Receta;
use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
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

    /** Producción editable por día: ['YYYY-MM-DD' => 50, ...] */
    public array $produccionEditada = [];

    /** Resultado calculado para la tabla */
    public array $filas = [];

    /** Resúmenes */
    public float $harinaTotalKg = 0.0;
    public int $turnosTotales = 0;
    public int $hornosSugeridos = 0;

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
            Select::make('productoId')
                ->label('Producto')
                ->options(Producto::query()->orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required()
                ->live(),

            DatePicker::make('desde')
                ->label('Desde')
                ->required()
                ->live(),

            DatePicker::make('hasta')
                ->label('Hasta')
                ->required()
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
        if (in_array($name, ['productoId', 'desde', 'hasta', 'turnosPorHornoPorDia'], true)) {
            $this->recalcular();
        }
    }

    public function recalcular(): void
    {
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
            $produccionTotal += $produccion;

            $this->filas[] = [
                'fecha' => $fecha,
                'inv_inicial' => $invInicial,
                'demanda' => $demanda,
                'prod_sugerida' => $produccionSugerida,
                'prod' => $produccion,
                'inv_final' => $invFinal,
                'turnos' => $turnos,
                'alerta' => $invFinal < $minStock,
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
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
