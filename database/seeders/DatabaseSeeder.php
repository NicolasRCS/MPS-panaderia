<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario admin simple
        \App\Models\User::firstOrCreate([
            'email' => 'admin@panaderia.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('demo123'),
        ]);

        // Productos simples
        $productosData = [
            ['nombre' => 'Baguette', 'unidad' => 'unidad', 'stock_inicial' => 20, 'stock_minimo' => 5, 'tamano_lote' => 10, 'capacidad_por_turno' => 10],
            ['nombre' => 'Medialuna', 'unidad' => 'unidad', 'stock_inicial' => 30, 'stock_minimo' => 5, 'tamano_lote' => 10, 'capacidad_por_turno' => 10],
        ];
        foreach ($productosData as $p) {
            \App\Models\Producto::firstOrCreate(['nombre' => $p['nombre']], $p);
        }

        $productos = \App\Models\Producto::all();

        // Pedidos simples para los próximos 3 días
        $start = now();
        foreach ([0,1,2] as $i) {
            foreach ($productos as $producto) {
                \App\Models\Pedido::create([
                    'fecha' => $start->copy()->addDays($i)->toDateString(),
                    'producto_id' => $producto->id,
                    'cantidad' => 5 + $i,
                    'estado' => 'nuevo',
                ]);
            }
        }

        // Hornos simples
        $hornos = [
            ['nombre' => 'Horno Demo', 'tipo' => 'convencional', 'capacidad_por_turno' => 20],
        ];
        foreach ($hornos as $h) {
            \App\Models\Horno::firstOrCreate(['nombre' => $h['nombre']], $h);
        }
    }
}
