<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            DivisiSeeder::class,
            PrioritasSeeder::class,
            StatusSeeder::class,
            CreateUserSeeder::class,
        ]);
    }
}
