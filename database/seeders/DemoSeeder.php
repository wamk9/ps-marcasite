<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Configurations;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ranium\SeedOnce\Traits\SeedOnce;

class DemoSeeder extends Seeder
{
    use SeedOnce;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'admin']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 1',
            'email' => 'aluno1@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 2',
            'email' => 'aluno2@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 3',
            'email' => 'aluno3@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 4',
            'email' => 'aluno4@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 5',
            'email' => 'aluno5@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 6',
            'email' => 'aluno6@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 7',
            'email' => 'aluno7@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 8',
            'email' => 'aluno8@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);
        User::create([
            'name' => 'Aluno 9',
            'email' => 'aluno9@marcasite.com.br',
            'password' => Hash::make('123456'),
            'role_id' => (Role::where([['name', '=', 'user']])->first()->id)
        ]);



        Course::create([
            'name' => 'Curso de UX',
            'value' => '1200.00',
            'active' => true,
            'registration_at' => '2024-02-01',
            'registration_until' => '2024-12-30',
            'category_id' => (Category::where([['name', '=', 'Design Gráfico']])->first()->id),
            'description' => "Curso de UX atualizado",
            'vacations' => 30
        ]);

        Course::create([
            'name' => 'Curso de Photoshop',
            'value' => '1000.00',
            'active' => true,
            'description' => "Curso Photoshop Descrição",
            'registration_at' => '2024-02-01',
            'registration_until' => '2024-12-30',
            'category_id' => (Category::where([['name', '=', 'Design Gráfico']])->first()->id),
            'vacations' => 30
        ]);

        Course::create([
            'name' => 'Curso de PHP',
            'value' => '2000.00',
            'active' => true,
            'registration_at' => '2024-06-01',
            'registration_until' => '2024-12-30',
            'category_id' => (Category::where([['name', '=', 'Programação']])->first()->id),
            'description' => "Curso de PHP atualizado 2024",
            'vacations' => 25
        ]);

        Course::create([
            'name' => 'Curso de JS',
            'value' => '1500.00',
            'active' => true,
            'registration_at' => '2024-07-01',
            'registration_until' => '2024-12-30',
            'category_id' => (Category::where([['name', '=', 'Programação']])->first()->id),
            'description' => "Curso de JS para testes",
            'vacations' => 50
        ]);

        Course::create([
            'name' => 'Curso de Coach',
            'value' => '900.00',
            'active' => false,
            'registration_at' => '2024-03-01',
            'registration_until' => '2024-12-30',
            'category_id' => (Category::where([['name', '=', 'Outros']])->first()->id),
            'description' => "Curso desativado",
            'vacations' => 40
        ]);
    }
}
