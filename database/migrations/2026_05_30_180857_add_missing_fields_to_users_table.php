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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student');
            $table->string('school')->nullable();
            $table->string('image')->nullable();
            $table->string('phone')->nullable();
            $table->string('area')->nullable();
            $table->string('status')->default('allowed');
            $table->timestamp('blocked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'school',
                'image',
                'phone',
                'area',
                'status',
                'blocked_at',
            ]);
        });
    }
};
