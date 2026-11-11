<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('user')->after('password'); // user | admin
            $t->boolean('is_blocked')->default(false)->after('role');
        });
        Schema::create('regions', function (Blueprint $t) {
            $t->id(); $t->string('slug')->unique(); $t->string('name'); $t->string('group');
            $t->decimal('center_lat',10,6); $t->decimal('center_lng',10,6);
            $t->decimal('min_lat',10,6); $t->decimal('max_lat',10,6);
            $t->decimal('min_lng',10,6); $t->decimal('max_lng',10,6);
            $t->timestamps();
        });
        Schema::create('safety_hubs', function (Blueprint $t) {
            $t->id(); $t->string('region_slug')->index(); $t->string('name');
            $t->string('type'); // polisi | satpam | faskes | halte
            $t->decimal('lat',10,6); $t->decimal('lng',10,6);
            $t->string('address')->nullable(); $t->boolean('is_24h')->default(true);
            $t->string('phone')->nullable(); $t->timestamps();
        });
        Schema::create('reports', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('region_slug')->index();
            $t->string('category'); // penerangan_padam | area_sepi | hambatan_fisik
            $t->decimal('lat',10,6); $t->decimal('lng',10,6);
            $t->decimal('user_lat',10,6)->nullable(); $t->decimal('user_lng',10,6)->nullable();
            $t->string('description')->nullable(); $t->string('photo_path')->nullable();
            $t->string('status')->default('UNVERIFIED'); // UNVERIFIED|VERIFIED|PENDING_MODERATION|RESOLVED|EXPIRED|HIDDEN
            $t->unsignedInteger('upvotes_count')->default(0);
            $t->json('ai_check')->nullable(); $t->boolean('is_simulation')->default(false);
            $t->timestamp('expires_at')->nullable(); $t->timestamp('verified_at')->nullable();
            $t->timestamps(); $t->index(['region_slug','status']); $t->index(['lat','lng']);
        });
        Schema::create('report_upvotes', function (Blueprint $t) {
            $t->id(); $t->foreignId('report_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamps(); $t->unique(['report_id','user_id']);
        });
        Schema::create('sos_alerts', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('region_slug')->nullable(); $t->decimal('lat',10,6)->nullable(); $t->decimal('lng',10,6)->nullable();
            $t->string('status')->default('open'); $t->string('message')->nullable(); $t->timestamps();
        });
        Schema::create('shared_routes', function (Blueprint $t) {
            $t->id(); $t->string('token')->unique(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('region_slug'); $t->string('mode')->default('motor');
            $t->decimal('origin_lat',10,6); $t->decimal('origin_lng',10,6);
            $t->decimal('destination_lat',10,6); $t->decimal('destination_lng',10,6);
            $t->json('route_data')->nullable(); $t->timestamp('expires_at')->nullable(); $t->timestamps();
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->id(); $t->string('key')->unique(); $t->string('value')->default('0'); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('settings'); Schema::dropIfExists('shared_routes');
        Schema::dropIfExists('sos_alerts'); Schema::dropIfExists('report_upvotes');
        Schema::dropIfExists('reports'); Schema::dropIfExists('safety_hubs'); Schema::dropIfExists('regions');
        Schema::table('users', function (Blueprint $t) { $t->dropColumn(['role','is_blocked']); });
    }
};
