<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shared_routes', function (Blueprint $table): void {
            $table->string('selected_route', 20)->nullable()->after('route_data');
        });
    }

    public function down(): void
    {
        Schema::table('shared_routes', function (Blueprint $table): void {
            $table->dropColumn('selected_route');
        });
    }
};
