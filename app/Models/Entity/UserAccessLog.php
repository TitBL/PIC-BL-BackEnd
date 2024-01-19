<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessLog extends Model
{
    use HasFactory;
    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_access_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'browser',
        'ip',
        'token',
        'expired_token',
        'request',
        'response',
        'created_at',
        'updated_at'
    ];
}
