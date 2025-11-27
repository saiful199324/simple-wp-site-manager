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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();             // mysite.com

            $table->string('server_host');                  // 167.172.86.3
            $table->unsignedSmallInteger('server_port')->default(22);
            $table->string('server_user')->default('root');

            $table->string('db_name');
            $table->string('db_user');
            $table->string('db_password');

            $table->string('container_name')->unique();     // e.g. wp_mysite_com

            $table->enum('status', ['running','stopped','deploying','failed'])
                ->default('deploying');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
