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
        // Create admin user (only if not exists)
        \App\Models\User::firstOrCreate([
            'email' => 'admin@panaderia.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password123'),
        ]);

        // Create several productos (if they don't exist)
        $productosData = [
            ['nombre' => 'Pan Frances', 'unidad' => 'unidad', 'stock_inicial' => 100, 'stock_minimo' => 10, 'tamano_lote' => 50, 'capacidad_por_turno' => 25],
            ['nombre' => 'Pan Integral', 'unidad' => 'unidad', 'stock_inicial' => 80, 'stock_minimo' => 8, 'tamano_lote' => 40, 'capacidad_por_turno' => 20],
            ['nombre' => 'Baguette', 'unidad' => 'unidad', 'stock_inicial' => 120, 'stock_minimo' => 15, 'tamano_lote' => 60, 'capacidad_por_turno' => 30],
            ['nombre' => 'Medialuna', 'unidad' => 'unidad', 'stock_inicial' => 200, 'stock_minimo' => 20, 'tamano_lote' => 100, 'capacidad_por_turno' => 50],
            ['nombre' => 'Pan de Avena', 'unidad' => 'unidad', 'stock_inicial' => 60, 'stock_minimo' => 6, 'tamano_lote' => 30, 'capacidad_por_turno' => 15],
        ];

        foreach ($productosData as $p) {
            \App\Models\Producto::firstOrCreate(['nombre' => $p['nombre']], $p);
        }

        $productos = \App\Models\Producto::all();

        // Create 50 pedidos distributed over the next 14 days
        $start = now();
        for ($i = 0; $i < 50; $i++) {
            $fecha = $start->copy()->addDays($i % 14)->toDateString();
            $producto = $productos->random();
            \App\Models\Pedido::create([
                'fecha' => $fecha,
                'producto_id' => $producto->id,
                'cantidad' => rand(5, 80),
                'estado' => 'nuevo',
            ]);
        }

        // Seed hornos (if not present)
        $hornos = [
            ['nombre' => 'Horno A', 'tipo' => 'convencional', 'capacidad_por_turno' => 100],
            ['nombre' => 'Horno B', 'tipo' => 'rotativo', 'capacidad_por_turno' => 250],
            ['nombre' => 'Horno Mini', 'tipo' => 'compacto', 'capacidad_por_turno' => 40],
        ];

        foreach ($hornos as $h) {
            \App\Models\Horno::firstOrCreate(['nombre' => $h['nombre']], $h);
        }
    }
}
