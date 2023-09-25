<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Criar o usuário admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('123'),
            'celular' => '11968458974',
            'cpf' => '02598745874'
        ]);

        // Obter a role de admin
        $adminRole = Role::where('name', 'admin')->first();

        // Se a role de admin existir, atribua-a ao usuário admin
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }
}
