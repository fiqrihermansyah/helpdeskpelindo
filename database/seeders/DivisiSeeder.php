<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisiSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('divisi')->truncate(); // Clear the table first to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        DB::table('divisi')->insert([
            ['id' => 1, 'nama_divisi' => 'IT Department', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_divisi' => 'Operation', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_divisi' => 'Finance', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_divisi' => 'Commercial', 'created_at' => now(), 'updated_at' => now()],
            // Add more divisions as necessary
        ]);
    }
}
