<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan Anda sudah membuat model App\Models\User
use App\Models\Role; // Pastikan Anda sudah membuat model App\Models\Role
use Illuminate\Support\Facades\Hash; // Digunakan untuk hashing password jika Anda memberi password plaintext

class UserSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // Pastikan roles sudah ada sebelum membuat user dan menetapkan role
        // Anda bisa memanggil RoleSeeder di sini, atau memastikannya dipanggil di DatabaseSeeder

        // Data pengguna dari db_ticketing.sql
        $usersData = [
            // Contoh Admin user
            [
                'username' => 'admin',
                'password' => '$2y$10$r4CX2xYPN33RtY3ZrLOpJ.4DheSHUqLzf5cVDNE0CyMp4cKpy9HaG', // Hash password dari db_ticketing.sql
                'roles' => ['Admin', 'Requester'] // Peran berdasarkan user_roles di db_ticketing.sql
            ],
            [
                'username' => 'Arief Sopian',
                'password' => '$2y$10$r4CX2xYPN33RtY3ZrLOpJ.4DheSHUqLzf5cVDNE0CyMp4cKpy9HaG',
                'roles' => ['Assignee'] // Peran berdasarkan user_roles di db_ticketing.sql
            ],
            [
                'username' => 'Syifa Nauval',
                'password' => '$2y$10$sz4TfHqk7jCy8X.AfXcILuPtMGZz8O8tUK2yaxb9UssAaCKJdacZG',
                'roles' => ['Assignee']
            ],
            [
                'username' => 'Fahmi Kurniawan',
                'password' => '$2y$10$HCXNg3J4qFA9o.ukcWV7/ugRdMRDQExB88T1ICeEJhC0L1Lcjrvj.',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Tony Budiman',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Bakti Setyo Prajanto',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Budi Arie Wibowo',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Muhammad Ilya Asha Soegondo',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Tsabit Alifudin',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'luckita.jackaria@pelita-air.com',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Diana Wenny Pawestri',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'M Agung Prasetyo',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Oktavianus Aji',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Rudi Nurhidayanto',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Ammyra Fatma Rizky',
                'password' => '$2y$10$A9yJ8L2F2u.N3.s9oVpQye.5k2R9wZ5z3/l8X.p9oW8u3t4VqG/a6',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Ghifari Kautsar',
                'password' => '$2y$10$ocMaSW9l4rm7YNRrFSVZUu1j9z5cUvID3YqfUjSLKru3Mi9FGjpH.',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Arya Zaenal Risyad',
                'password' => '$2y$10$ctPImk9EjtASAR7GNDJBKO3RE3WJt7Qqlp2uAzJIB4D4vraySiybK',
                'roles' => ['Requester']
            ],
            [
                'username' => 'Faisal Ibnu M',
                'password' => '$2y$10$pyhWzOd0tJsAY8P5Z.T0wOpKp.M9p8TBKeDvygUHGduCfi2Uf4WAa',
                'roles' => ['Requester']
            ],
        ];

        foreach ($usersData as $userData) {
            // Mencoba menemukan user berdasarkan username, jika tidak ada, buat yang baru
            $user = User::firstOrCreate(
                ['username' => $userData['username']],
                ['password' => $userData['password']]
            );

            // Menetapkan peran (role) untuk user
            if (!empty($userData['roles'])) {
                $rolesToAttach = Role::whereIn('name', $userData['roles'])->get();
                // Hanya attach role yang belum dimiliki user
                $user->roles()->syncWithoutDetaching($rolesToAttach->pluck('id'));
            }
        }
    }
}