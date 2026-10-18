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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('short_name', 30)->nullable()->after('id');
            $table->string('staff_theme_color', 7)->nullable()->default('#0f172a')->after('short_name');
            $table->string('user_theme_color', 7)->nullable()->default('#4f46e5')->after('staff_theme_color');
            $table->string('staff_icon_192')->nullable()->after('user_theme_color');
            $table->string('staff_icon_512')->nullable()->after('staff_icon_192');
            $table->string('user_icon_192')->nullable()->after('staff_icon_512');
            $table->string('user_icon_512')->nullable()->after('user_icon_192');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'short_name',
                'staff_theme_color',
                'user_theme_color',
                'staff_icon_192',
                'staff_icon_512',
                'user_icon_192',
                'user_icon_512',
            ]);
        });
    }
};
