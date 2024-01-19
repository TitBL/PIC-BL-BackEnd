<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'token',
        'expired',
        'state',
        'created_at',
        'updated_at'
    ];
}
