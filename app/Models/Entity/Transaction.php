<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key_access_sri',
        'emission_type',
        'emission_date',
        'document_type',
        'document_number',
        'document_issuer_id',
        'document_issuer_name',
        'document_receiver_id',
        'document_receiver_name',
        'date_time_authorization',
        'date_time_last_query',
        'amount',
        'state',
        'created_at',
        'updated_at'
    ];

    public function findByID(string $clave)
    {
        if (empty($clave) ) {
            throw new \InvalidArgumentException("clave cannot be empty");
        }
 
        return $this->select('key_access_sri')
            ->where('key_access_sri', '=', $clave) 
            ->first();
    }
}
