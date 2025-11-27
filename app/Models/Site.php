<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;
    protected $fillable = [
        'name','domain',
        'server_host','server_port','server_user',
        'db_name','db_user','db_password',
        'container_name','status','monitor_token','http_port','last_reported_at',
    ];

    protected $hidden = [
        'monitor_token',
    ];

    // encrypt sensitive fields at rest
    protected $casts = [
        'server_host' => SafeEncrypted::class,
        'server_user' => SafeEncrypted::class,
        'db_name' => SafeEncrypted::class,
        'db_user' => SafeEncrypted::class,
        'db_password' => SafeEncrypted::class,
        'server_port' => 'integer',
        'http_port' => 'integer',
        'last_reported_at' => 'datetime',
    ];
}
