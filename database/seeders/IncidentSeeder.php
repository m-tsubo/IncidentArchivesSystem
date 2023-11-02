<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // デフォルトのユーザーIDと部署IDを仮に設定
        // 実際の環境に合わせてこれらの値を適切に変更してください。
        $defaultUserId = 1;
        $defaultDepartmentId = 1;

        foreach (range(1, 10) as $index) {
            DB::table('incidents')->insert([
                'case_name' => 'Case Name ' . $index,
                'detail_path' => null, // 仮の値。アップロードロジックに基づいて適切に設定してください。
                'order_number' => 'Order' . $index,
                'person_in_charge' => 'Person ' . $index,
                'department_id' => $defaultDepartmentId,
                'incident' => 'Incident description for ' . $index,
                'solution' => 'Solution description for ' . $index,
                'user_id' => $defaultUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
