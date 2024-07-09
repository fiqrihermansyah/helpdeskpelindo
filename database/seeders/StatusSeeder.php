<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run()
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete existing data
        DB::table('status')->delete();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('status')->insert([
            ['id' => 1, 'nama_status' => 'Open', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_status' => 'In Progress', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_status' => 'Closed', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
