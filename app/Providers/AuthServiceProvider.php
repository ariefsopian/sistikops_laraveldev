<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Import Facade Gate
use App\Models\User; // Import model User

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Daftarkan Gate 'manage-users'
        // Gate ini akan menerima instance User yang sedang login
        Gate::define('manage-users', function (User $user) {
            // Periksa apakah user memiliki peran 'Admin'
            // Method isAdmin() ini berasal dari model User yang sudah kita definisikan
            return $user->isAdmin();
        });
    }
}
