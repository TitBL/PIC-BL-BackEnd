<?php

namespace App\Models\Entity;

use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Pivot\UserCompanyPivot;
use App\Models\Entity\Rol as ModelRol;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'rol_id',
        'DNI',
        'name',
        'full_name',
        'address',
        'email',
        'created_user',
        'updated_user',
        'created_at',
        'updated_at',
        'state'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Find the entity by DNI.
     *
     * @param int $id The DNI of the user to find.
     *
     * @return Collection The user entity with associated.
     */
    public function findByDNI($DNI): Collection
    {
        $user = $this->getViewUser()
            ->where('users.state', '=', true)
            ->where('users.DNI', 'LIKE', '%' . $DNI . '%')
            ->first();

        // Check if the user is found.
        if ($user) {
            $termsAndConditions = UsersTermsConditions::select('id', 'details_terms as Terminos', 'date_time_terms_accept as [FechaAceptado]')
                ->where('user_id', '=', $user->id)
                ->where('terms_accept', '=', true)
                ->get();

            $user->put('TerminosCondiciones', $termsAndConditions);
        }
        return $user;
    }

    /**
     * Find the entity by DNI.
     *
     * @param string $DNI The DNI of the user to find.
     *
     */
    public function getIdForDNI($DNI)
    {
        return $this->select('id')
            ->where('DNI', '=', $DNI)
            ->first();
    }


    /**
     * Find the entity by Id.
     *
     * @param int $id The Id of the user to find.
     *
     * @return Collection The user entity with associated.
     */
    public function findById($id): Collection
    {
        $user = $this->getUser()
            ->where('state', '=', true)
            ->where('id', '=', $id)
            ->first();

        if ($user) {

            $companyIds = UserCompany::select("company_id")
                ->where('user_id', '=', $id)
                ->pluck('permission_id')
                ->map(function ($companyId) {
                    return (int)$companyId;
                })
                ->toArray();

            //terms and conditions
            $termsAndConditions = new  UsersTermsConditions();

            $obj = new Collection($user->toArray());
            $obj->put('Empresas', $companyIds);
            $obj->put('TerminosCondiciones', $termsAndConditions->getByIdUser($id));
            return $obj;
        } else {
            throw new ModelNotFoundException("User with ID $id not found.");
        }
    }

    public function createConsummer($dni, $name, $adress, $email)
    {

        $newEntity = new User();
        $newEntity->rol_id = 0;
        $newEntity->DNI = $dni;
        $newEntity->name = Str::random(20);
        $newEntity->full_name = $name;
        $newEntity->address = $adress;
        $newEntity->email = $email;
        $newEntity->password = hashPWD("nnnnnn");
        $newEntity->created_user = 0;
        $newEntity->save();

        return $newEntity;
    }

    /**
     * Convert de User for Entity login
     *
     * @param int $id The Id of the user to find.
     *
     * @return Collection The user entity with associated.
     */
    public function castLoginEntity($authUser): Collection
    {
        $user = new Collection();
        $user->put('Id', $authUser->id);
        $user->put('DNI', $authUser->DNI);
        $user->put('NombreCompleto', $authUser->full_name);
        $user->put('Email', $authUser->email);


        $roles = new ModelRol();
        $objRol = $roles->getWithPermissions($authUser->rol_id);
        $user->put('IdRol', $authUser->rol_id);
        $user->put('NombreRol', $objRol->get('name'));
        $user->put('Permisos ', $objRol->get('permissions'));

        $companyIds = UserCompany::select("company_id")
            ->where('user_id', '=', $authUser->id)
            ->pluck('permission_id')
            ->map(function ($companyId) {
                return (int)$companyId;
            })
            ->toArray();

        $user->put('Empresas', $companyIds);
        return $user;
    }

    /**
     * Get users filtered by the specified state.
     *
     * @param bool $state The state to filter users by.
     * @return Collection A collection of users filtered by the specified state.
     */
    public function getByState(bool $state): Collection
    {
        return $this->getViewUser()
            ->where('users.state', '=', $state)
            ->take(30)
            ->get();
    }

    /**
     * Get companies filtered by RUC, company name, or commercial name.
     *
     * @param string $filter The filter criteria for RUC, company name, or commercial name.
     * @return Collection The user entity with associated.
     */
    public function getByFilter(string $filter): Collection
    {
        return $this->getViewUser()
            ->where('users.state', '=', true)
            ->where(function ($query) use ($filter) {
                $query->where('DNI', 'LIKE', "%$filter%")
                    ->orWhere('users.name', 'LIKE', "%$filter%")
                    ->orWhere('users.email', 'LIKE', "%$filter%");
            })
            ->take(30)
            ->get();
    }

    /**
     * Get the model for the query.
     *
     * @return Builder The Eloquent query builder for the model.
     */
    private function getViewUser()
    {
        // This method defines a query on the model with specific columns selected and renamed.
        return $this->select('users.id', 'users.DNI', 'users.full_name as NombreUsuario', 'users.email', 'roles.name as Rol', 'users.state as estado')
            ->join('roles', 'users.rol_id', '=', 'roles.id');
    }

    /**
     * Get the model for the query.
     *
     * @return Builder The Eloquent query builder for the model.
     */
    private function getUser()
    {
        // This method defines a query on the model with specific columns selected and renamed.
        return $this->select('id', 'rol_id as IdRol', 'DNI', 'name as NombreUsuario', 'full_name as NombreCompleto', 'address as Direccion', 'email as Email');
    }

    /**
     * Define the many-to-many relationship with the Permission model.
     *
     * @param int $userId The ID of the user creating or updating the pivot records.
     * @return BelongsToMany The Eloquent relationship.
     */
    public function companies($userId)
    {
        return $this->belongsToMany(UserCompany::class, 'users_companies', 'user_id', 'company_id')
            ->withPivot('date_time_terms')
            ->withTimestamps()
            ->using(UserCompanyPivot::class)
            ->withPivotValue('created_user', $userId)
            ->withPivotValue('updated_user', $userId);
    }
}
