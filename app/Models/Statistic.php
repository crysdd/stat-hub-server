<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'user_agent',
        'browser_name',
        'browser_version',
        'os_name',
        'screen_resolution',
        'color_depth',
        'language',
        'page_url',
        'client_ip',
        'referrer',
    ];

    // You can add accessors for nicer formatting
    public function getBrowserInfoAttribute()
    {
        return $this->browser_name . ' ' . $this->browser_version;
    }
}
