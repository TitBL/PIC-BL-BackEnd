<?php

namespace App\Models\Entity;

use App\Models\Pivot\RolPermissionPivot;
use App\Enums\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Rol extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'created_user',
        'updated_user',
        'created_at',
        'updated_at',
        'state'
    ];

    /**
     * Find the entity by ID.
     *
     * @param int $id The ID of the role to find.
     *
     * @return Collection The role entity with associated permissions.
     */
    public function getWithPermissions($id)
    {
        $obj = new Collection();
        $rolname = "";
        $permisosIds = [];
        switch ($id) {
            case 0:
                $rolname = CONSUMER_NAME;
                $permisosIds = Permissions::getListConsumerPermissions()->pluck('id')->toArray();
                $obj->put('permisos', $permisosIds);
                break;
            case 1:
                $rolname = MASTER_NAME;
                $permisosIds = Permissions::getListMasterPermissions()->pluck('id')->toArray();
                $obj->put('permisos', $permisosIds);
                break;
            default:
                $rolname = $this->Select('name')
                    ->where('state', '=', true)
                    ->findOrFail($id)->name;

                $permisosIds = RolPermission::select("permission_id")
                    ->where('rol_id', '=', $id)
                    ->pluck('permission_id')
                    ->map(function ($permissionId) {
                        return (int)$permissionId;
                    })
                    ->toArray();

                break;
        }

        $obj->put('name', $rolname);
        $obj->put('permissions', $permisosIds);
        return  $obj;
    }




    /**
     * Find the entity by ID.
     *
     * @param int $id The ID of the role to find.
     *
     * @return Collection The role entity with associated permissions.
     */
    public function findByID($id)
    {
        $rol = $this->getViewRol()
            ->where('state', '=', true)
            ->findOrFail($id);

        $permisosIds = RolPermission::select("permission_id")
            ->where('rol_id', '=', $id)
            ->pluck('permission_id')
            ->map(function ($permissionId) {
                return (int)$permissionId;
            })
            ->toArray();

        $obj = new Collection($rol->toArray());
        $obj->put('permisos', $permisosIds);

        return $obj;
    }


    /**
     * Get roles filtered by state.
     *
     * @param bool $state The state to filter roles by.
     * @return Collection The collection of roles matching the specified state.
     */
    public function getByState(bool $state)
    {
        return $this->getViewRol()
            ->where('state', '=', $state)
            ->whereNotIn('id', [0, 1])
            ->take(30)
            ->get();
    }


    /**
     * Get the model for the query.
     *
     * @return Builder The Eloquent query builder for the model.
     */
    private function getViewRol()
    {
        // This method defines a query on the model with specific columns selected and renamed.
        return $this->select('id', 'name as Nombre', 'description  as Descripcion', 'state as estado');
    }

    /**
     * Obtener roles filtradas por nombre.
     * 
     * @param string $filter
     * @return Array
     */
    public function getByFilter(string $filter)
    {
        return $this->getViewRol()
            ->where('state', '=', true)
            ->where('name', 'LIKE', "%$filter%")
            ->whereNotIn('id', [0, 1])
            ->take(30)
            ->get();
    }



    /**
     * Define la relación muchos a muchos con el modelo Permission.
     */
    public function permissions($userId)
    {
        return $this->belongsToMany(RolPermission::class, 'rol_permissions', 'rol_id', 'permission_id')
            ->withPivot('created_user', 'updated_user') // Incluir campos adicionales en la tabla pivote
            ->withTimestamps()
            ->using(RolPermissionPivot::class)
            ->withPivotValue('created_user', $userId)
            ->withPivotValue('updated_user', $userId);
    }

    /**
     * Define la relación muchos a muchos con el modelo Permission.
     */
    public function rolpermissions()
    {
        return $this->belongsToMany(Rol::class, 'rol_permissions', 'permission_id', 'rol_id');
    }
}
