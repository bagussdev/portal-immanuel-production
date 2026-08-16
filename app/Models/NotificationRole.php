<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class NotificationRole extends Pivot
{
    protected $table = 'notification_roles';

    public $timestamps = true;

    protected $fillable = ['notification_id', 'role_id'];
}
