<?php

namespace App\Filament\Admin\Pages;

use App\Models\Horno;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use BackedEnum;

class Hornos extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Hornos';
    protected static ?string $title = 'Hornos';

    public ?string $nombre = null;
    public ?string $tipo = null;
    public float $capacidad_por_turno = 0.0;

    public function getView(): string
    {
        return 'filament.admin.hornos';
    }

    public function form(Schema $schema)
    {
        return $schema->components([
            TextInput::make('nombre')->required()->label('Nombre'),
            TextInput::make('tipo')->label('Tipo'),
            TextInput::make('capacidad_por_turno')->label('Capacidad por turno')->numeric()->minValue(0)->required(),
        ]);
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    public function storeHorno(): void
    {
        $data = $this->form->getState();

        Horno::create([
            'nombre' => $data['nombre'] ?? $this->nombre,
            'tipo' => $data['tipo'] ?? $this->tipo,
            'capacidad_por_turno' => $data['capacidad_por_turno'] ?? $this->capacidad_por_turno,
        ]);

        Notification::make()->title('Horno guardado')->success()->send();

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'nombre' => null,
            'tipo' => null,
            'capacidad_por_turno' => 0,
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
