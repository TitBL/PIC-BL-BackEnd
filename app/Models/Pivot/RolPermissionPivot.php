<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolPermissionPivot extends Pivot
{
    protected $casts = [
        'created_user' => 'integer',
        'updated_user' => 'integer',
    ];
}
