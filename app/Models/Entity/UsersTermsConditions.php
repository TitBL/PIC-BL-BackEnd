<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UsersTermsConditions extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_terms_conditions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'details_terms',
        'terms_accept',
        'created_at',
        'updated_at'
    ];

    /**
     * Find the entity by Id.
     *
     * @param int $id The Id of the user to find. 
     * 
     * @return Collection The user entity with associated.
     */
    public function getByIdUser($idUser)
    {
        $terms = $this->select('details_terms as Terminos', 'terms_accept as Aceptado', 'created_at as FechaRegistro')
            ->where('user_id', '=', $idUser)
            ->orderBy('created_at', 'desc')
            ->first();
        return $terms;
    }
}
