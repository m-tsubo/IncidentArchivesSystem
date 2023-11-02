<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Incident;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // ハッシュの名前空間を追加

class InitDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 外部キー制約のチェックを無効にする
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // テーブルの内容をすべて削除する
        Incident::truncate();
        User::truncate();

        // 外部キー制約のチェックを有効にする
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ユーザーのサンプルデータを5人分作成
        $departments = Department::all();

        $users = [
            ['name' => '坪山 元春', 'email' => 'm-tsuboyama@showa-print.com', 'password' => '00001234', 'department_id' => 4],
            ['name' => '赤松 剛', 'email' => 'g-akamatsu@showa-print.com', 'password' => '00001111', 'department_id' => 4],
            ['name' => '東方 仁司', 'email' => 'j-higashikata@showa-print.com', 'password' => '00001112', 'department_id' => 4],
            ['name' => '今 達之', 'email' => 't-kon@showa-print.com', 'password' => '00001113', 'department_id' => 4],
            ['name' => '石橋 直人', 'email' => 'n-ishibashi@showa-print.com', 'password' => '00001114', 'department_id' => 4],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt($user['password']),
                'department_id' => $user['department_id'], // 手動で指定した部署ID
            ]);
        }
    }
}
