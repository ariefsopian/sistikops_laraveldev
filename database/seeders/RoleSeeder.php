<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // Pastikan Anda sudah membuat model App\Models\Role

class RoleSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // Data peran yang ada di db_ticketing.sql
        $roles = [
            'Admin',
            'Assignee',
            'Requester',
            'User',
        ];

        foreach ($roles as $roleName) {
            // firstOrCreate akan membuat role hanya jika belum ada, mencegah duplikasi
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}