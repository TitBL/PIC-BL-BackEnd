<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 
        'user_id', 
        'company_id', 
        'created_user', 
        'updated_user', 
        'created_at', 
        'updated_at'
    ];
}
