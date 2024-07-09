<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritasSeeder extends Seeder
{
    public function run()
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete existing data
        DB::table('prioritas')->delete();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert seed data
        DB::table('prioritas')->insert([
            ['id' => 1, 'nama_prioritas' => 'High', 'value' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_prioritas' => 'Medium', 'value' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_prioritas' => 'Low', 'value' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
