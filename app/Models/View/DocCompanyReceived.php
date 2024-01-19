<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Collection;

class DocCompanyReceived extends Model
{
    protected $table = 'doc_company_received';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Anio',
        'Mes',
        'Dia',
        'Fecha Documento',
        'Fecha y Hora Autorizacion',
        'Emisor',
        'Tipo Documento',
        'Documento',
        'Clave Acceso'
    ];

    /**
     * Gets a list of documents for a user and date range using a stored procedure.
     *
     * @param int $id - Company ID.
     * @param string $startDate - Start date of the range.
     * @param string $endDate - End date of the range.
     *
     * @return Collection - Collection of DocReceiver instances.
     */
    public static function getListByUserAndDateRange($id, $startDate, $endDate): Collection
    {
        $results = DB::select('EXEC sp_get_doc_company_received ?, ?, ?', [$id, $startDate, $endDate]);

        $renamedResults = array_map(function ($result) {
            return [
                'Anio' => $result->year,
                'Mes' => $result->month,
                'Dia' => $result->day,
                'Fecha Documento' => $result->emission_date,
                'Fecha y Hora Autorizacion' => $result->date_time_authorization,
                'Emisor' => $result->document_issuer,
                'Tipo Documento' => $result->document_type,
                'Documento' => $result->document_number,
                'Clave Acceso' => $result->key_access_sri,
            ];
        }, $results);
        return DocIssuerReceived::hydrate($renamedResults);
    }
}
