<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('departments')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $departments = [
            
            '営業１部',
            '営業２部',
            '大阪営業１部',
            '生産課',
            'デザイン課',
            '管理部',
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'name' => $department,
            ]);
        }
    }
}
