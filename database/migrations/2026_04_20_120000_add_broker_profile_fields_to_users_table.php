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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('broker_title')->nullable()->after('name');
            $table->string('phone', 30)->nullable()->after('password');
            $table->string('whatsapp', 30)->nullable()->after('phone');
            $table->string('creci', 50)->nullable()->after('whatsapp');
            $table->string('photo_path')->nullable()->after('creci');
            $table->string('broker_bio', 255)->nullable()->after('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'broker_title',
                'phone',
                'whatsapp',
                'creci',
                'photo_path',
                'broker_bio',
            ]);
        });
    }
};
