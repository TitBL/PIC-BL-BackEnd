<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCompanyPivot extends Pivot
{
    protected $casts = [
        'created_user' => 'integer',
        'updated_user' => 'integer',
    ];
}
