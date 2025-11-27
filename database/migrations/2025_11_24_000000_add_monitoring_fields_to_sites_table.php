<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedInteger('http_port')->default(8080)->after('container_name');
            $table->string('monitor_token', 64)->nullable()->unique()->after('status');
            $table->timestamp('last_reported_at')->nullable()->after('monitor_token');
        });

        // Backfill tokens for existing rows if any
        DB::table('sites')->whereNull('monitor_token')->orderBy('id')->chunk(100, function ($sites) {
            foreach ($sites as $site) {
                DB::table('sites')
                    ->where('id', $site->id)
                    ->update(['monitor_token' => Str::random(40)]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['http_port', 'monitor_token', 'last_reported_at']);
        });
    }
};
