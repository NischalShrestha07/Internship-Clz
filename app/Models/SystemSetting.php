<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable=['logo','name','title'];
    protected $table= 'system_settings';
}