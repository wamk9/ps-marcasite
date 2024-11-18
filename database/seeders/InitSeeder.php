<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Configurations;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;

class InitSeeder extends Seeder
{
    use SeedOnce;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin', 'friendly_name' => 'Admin']);
        Role::create(['name' => 'user', 'friendly_name' => 'Aluno']);

        Category::create(['name' => 'Programação']);
        Category::create(['name' => 'Design Gráfico']);
        Category::create(['name' => 'Outros']);

        Configurations::create(['key' => 'MP_PRIVATE_TOKEN']);
        Configurations::create(['key' => 'MP_PUBLIC_TOKEN']);

    }
}
