<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('case_name');
            $table->string('detail_path')->nullable(); // ここにアップロードされたファイルのパスを保存します
            $table->string('order_number');
            $table->string('person_in_charge');
            $table->unsignedBigInteger('department_id');
            $table->text('incident');
            $table->text('solution');
            $table->unsignedBigInteger('user_id'); // 追加: ユーザーIDを保存するためのカラム
            $table->timestamps();
    
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('user_id')->references('id')->on('users'); // 追加: usersテーブルへの外部キー制約
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
