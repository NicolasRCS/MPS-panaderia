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

        // Crear clientes de prueba
        $clientes = [
            [
                'numero_pedido' => 'PED001',
                'nombre' => 'Pizzería La Esquina',
                'telefono' => '+54 11 1234-5678',
                'direccion' => 'Av. Corrientes 1234, CABA',
                'observaciones' => 'Cliente habitual, entrega temprano'
            ],
            [
                'numero_pedido' => 'PED002',
                'nombre' => 'Panadería Central',
                'telefono' => '+54 11 2345-6789',
                'direccion' => 'Calle Florida 567, CABA',
                'observaciones' => 'Pago al contado'
            ],
            [
                'numero_pedido' => 'PED003',
                'nombre' => 'Confitería Dulce Hogar',
                'telefono' => '+54 11 3456-7890',
                'direccion' => 'San Martín 890, CABA',
                'observaciones' => 'Cliente premium'
            ],
            [
                'numero_pedido' => 'PED004',
                'nombre' => 'Cafetería del Barrio',
                'telefono' => '+54 11 4567-8901',
                'direccion' => 'Av. Santa Fe 2345, CABA',
                'observaciones' => 'Pedidos grandes los lunes'
            ],
        ];
        
        foreach ($clientes as $cliente) {
            \App\Models\Cliente::firstOrCreate(['numero_pedido' => $cliente['numero_pedido']], $cliente);
        }

        $clientesCreados = \App\Models\Cliente::all();

        // Pedidos con clientes y fechas variadas
        $estados = ['nuevo', 'en_produccion', 'finalizado', 'listo', 'entregado_al_cliente', 'cancelado'];
        $observaciones = [
            'Cliente habitual, siempre a tiempo',
            'Pedido grande, requiere embalar especial',
            'Prioridad alta, evento el domingo',
            'Nuevo solicitud, validar dirección de entrega',
            'Cliente canceló por cambio de planes',
            'Pedido estándar',
        ];
        $start = now();
        
        foreach ([0,1,2,3,4,5] as $i) {
            foreach ($productos as $producto) {
                \App\Models\Pedido::create([
                    'cliente_id' => $clientesCreados->random()->id,
                    'fecha' => $start->copy()->addDays($i)->toDateString(),
                    'fecha_carga' => $start->copy()->subDays(rand(1,3))->toDateString(),
                    'producto_id' => $producto->id,
                    'cantidad' => rand(3, 16),
                    'estado' => $estados[array_rand($estados)],
                    'observaciones' => $observaciones[array_rand($observaciones)],
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
