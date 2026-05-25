<?php

namespace Database\Seeders;

use App\Models\Frame;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@mringis.com'],
            [
                'name' => 'Admin Mringis',
                'email' => 'admin@mringis.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create sample frames
        $frames = [
            [
                'name' => 'Classic Strip',
                'description' => 'Template strip klasik dengan 4 foto vertikal',
                'slot_count' => 4,
                'slot_layout' => [
                    ['x' => 5, 'y' => 2, 'w' => 90, 'h' => 22],
                    ['x' => 5, 'y' => 26, 'w' => 90, 'h' => 22],
                    ['x' => 5, 'y' => 50, 'w' => 90, 'h' => 22],
                    ['x' => 5, 'y' => 74, 'w' => 90, 'h' => 22],
                ],
                'price' => 15000,
                'is_active' => true,
            ],
            [
                'name' => 'Duo Frame',
                'description' => 'Template dengan 2 foto berdampingan',
                'slot_count' => 2,
                'slot_layout' => [
                    ['x' => 2, 'y' => 5, 'w' => 46, 'h' => 90],
                    ['x' => 52, 'y' => 5, 'w' => 46, 'h' => 90],
                ],
                'price' => 10000,
                'is_active' => true,
            ],
            [
                'name' => 'Kolase 4x',
                'description' => 'Template kolase 2x2 dengan 4 foto',
                'slot_count' => 4,
                'slot_layout' => Frame::defaultLayouts()[4],
                'price' => 20000,
                'is_active' => true,
            ],
            [
                'name' => 'Galeri 6',
                'description' => 'Template kolase galeri dengan 6 foto',
                'slot_count' => 6,
                'slot_layout' => Frame::defaultLayouts()[6],
                'price' => 25000,
                'is_active' => true,
            ],
            [
                'name' => 'Solo Shot',
                'description' => 'Template satu foto penuh',
                'slot_count' => 1,
                'slot_layout' => Frame::defaultLayouts()[1],
                'price' => 8000,
                'is_active' => true,
            ],
        ];

        foreach ($frames as $frame) {
            Frame::updateOrCreate(
                ['name' => $frame['name']],
                $frame
            );
        }

        $this->command->info('✅ Admin user dan sample frames berhasil dibuat!');
        $this->command->info('   Admin: admin@mringis.com / password');
    }
}
