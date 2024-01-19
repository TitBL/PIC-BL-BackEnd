<?php

namespace App\Models\Entity;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'RUC',
        'company_name',
        'commercial_name',
        'patch_logo',
        'patch_folder',
        'can_send_email',
        'can_used_smtp',
        'smtp_server',
        'smtp_port',
        'smtp_email',
        'smtp_type_security',
        'smtp_user',
        'smtp_password',
        'api_key',
        'created_user',
        'updated_user',
        'created_at',
        'updated_at',
        'state'
    ];

    /** 
     * busca la entidad por ruc
     */
    public function findByRUC($ruc)
    {
        return  $this->getViewCompany()
            ->where('RUC', '=', $ruc)
            ->get();
    }

    /** 
     * busca la entidad por id
     */
    public function findByID($id)
    {
        return  $this->getViewCompany()
            ->where('id', '=', $id)
            ->get();
    }

    /**
     * Obtener el modelo de la consulta.
     */
    private function getViewCompany()
    {
        return $this->select('id', 'patch_logo as logo', 'RUC', 'company_name as razon_social', 'commercial_name as nombre_comercial', 'state as estado');
    }

    /**
     * Obtener empresas filtradas por estado.
     *
     * @param bool $state 
     * @return Collection
     */
    public function getByState(bool $state)
    {
        return $this->getViewCompany()
            ->where('state', '=', $state)
            ->take(30)
            ->get();
    }

    /**
     * Obtener empresas filtradas por RUC, company_name, commercial_name.
     * 
     * @param string $filter
     * @return Array
     */
    public function getByFilter(string $filter)
    {
        return $this->getViewCompany()
            ->where('state', '=', true)
            ->where(function ($query) use ($filter) {
                $query->where('RUC', 'LIKE', "%$filter%")
                    ->orWhere('company_name', 'LIKE', "%$filter%")
                    ->orWhere('commercial_name', 'LIKE', "%$filter%");
            })
            ->take(30)
            ->get();
    }

    /**
     * Retrieves data from the database based on API key and RUC.
     *
     * @param string $APIKey The API key to use in the query.
     * @param string $RUC The RUC to use in the query.
     *
     * @return mixed|null The result of the database query or null if no results found.
     *
     * @throws \InvalidArgumentException If $APIKey or $RUC is empty.
     * @throws \RuntimeException If no results are found for the query.
     */
    public function getByAPIKEY_RUC(string $APIKey, string $RUC)
    {
        // Check if $APIKey or $RUC is empty and throw an exception if 
        if (empty($APIKey) || empty($RUC)) {
            throw new \InvalidArgumentException("APIKey and RUC cannot be empty");
        }

        // Execute the query only if neither $APIKey nor $RUC is empty
        $result = $this->select('id', 'company_name')
        ->where('api_key', '=', $APIKey)
            ->where('RUC', '=', $RUC)
            ->first();

        // Check if the query did not return results and throw an exception
        if (!$result) {
            throw new \RuntimeException("No results found");
        }

        return $result;
    }

    /**
     * Generates a random API key for the company.
     *
     * @return string
     */
    public function generarAPIKey()
    {
        return Str::random(60);
    }
}
