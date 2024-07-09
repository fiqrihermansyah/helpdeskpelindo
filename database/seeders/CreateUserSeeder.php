<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate(); // Clear the table first to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = [
            [
                'nama' => 'Iqbal Maulana',
                'nipp' => 'iqbal',
                'password' => bcrypt('iqbal'),
                'divisi_id' => 1, // Ensure this matches the 'IT Department' ID from DivisiSeeder
                'status' => true,
                'nomor_hp' => '087884550875',
                'avatar' => 'https://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50?s=80&d=retro',
                'role' => 'user'
            ],
            [
                'nama' => 'Admin User',
                'nipp' => 'admin',
                'password' => bcrypt('admin'), // Replace with secure password
                'divisi_id' => 2, // Ensure this matches the 'Operation' ID from DivisiSeeder
                'status' => true,
                'nomor_hp' => '087884550876',
                'avatar' => 'https://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50?s=80&d=retro',
                'role' => 'admin'
            ],
            [
                'nama' => 'Rahman Hakim',
                'nipp' => 'rahman',
                'password' => bcrypt('rahman'), // Replace with secure password
                'divisi_id' => 2, // Ensure this matches the 'Operation' ID from DivisiSeeder
                'status' => true,
                'nomor_hp' => '087884550876',
                'avatar' => 'https://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50?s=80&d=retro',
                'role' => 'user'
            ],
            [
                'nama' => 'Akbar Frimawan',
                'nipp' => 'akbar',
                'password' => bcrypt('akbar'), // Replace with secure password
                'divisi_id' => 2, // Ensure this matches the 'Operation' ID from DivisiSeeder
                'status' => true,
                'nomor_hp' => '087884550876',
                'avatar' => 'https://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50?s=80&d=retro',
                'role' => 'user'
            ],
        ];

        foreach ($users as $user) {
            $roleName = $user['role'];
            unset($user['role']);
            $userModel = User::create($user);

            $roleId = DB::table('roles')->where('name', $roleName)->value('id');
            DB::table('role_user')->insert([
                'user_id' => $userModel->id,
                'role_id' => $roleId,
            ]);
        }
    }
}
