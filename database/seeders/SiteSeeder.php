<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Seed multiple demo site records (values stored as-is; no encryption here)
        DB::table('sites')->upsert([
            [
                'domain' => 'test.local',
                'name' => 'Test WP',
                'server_host' => '167.172.86.3',
                'server_port' => 22,
                'server_user' => 'root',
                'db_name' => 'wp_test',
                'db_user' => 'wp_user',
                'db_password' => 'secret',
                'container_name' => 'wp-test-wordpress-1',
                'http_port' => 8080,
                'status' => 'running',
                'monitor_token' => '0AtCDSQIo0M07BkHQFTaJj32ontvjoI1Cxxo8EHy',
                'last_reported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
        ], ['domain'], [
            'name',
            'server_host',
            'server_port',
            'server_user',
            'db_name',
            'db_user',
            'db_password',
            'container_name',
            'http_port',
            'status',
            'monitor_token',
            'last_reported_at',
            'updated_at',
        ]);
    }
}
