<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class RolPermission extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'rol_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'rol_id',
        'permission_id',
        'created_user',
        'updated_user',
        'created_at',
        'updated_at'
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permissions', 'permission_id', 'rol_id');
    }
}
