<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    use HasFactory;
    /**
     * Name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_notifications';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'send_type',
        'reference',
        'send_to',
        'sent_cc',
        'date_time_send',
        'observations',
        'state',
        'created_at',
        'updated_at'
    ];


    public function saveStatus(int $Id, int $Estado, string $Observacion = null)
    {
        $mailC = $this->find($Id);
        $mailC->state = $Estado;
        if (!is_null($Observacion)) {
            $mailC->observations =   $Observacion;
        }
        $mailC->save();
    }

    public   function getMailParam(int $id)
    {
        // return  DB::table('Mail_Control')
        // ->join('Transacciones', 'Transacciones.id', '=', 'Mail_Control.id_Transaccion')
        // ->join('Empresas', 'Empresas.id', '=', 'Transacciones.id_Empresa')
        // ->select('Mail_Control.id as Id', 'Transacciones.Cliente', 'Transacciones.Numero_Documento', 'Empresas.Patch_Logo', 'Empresas.Patch_Folder', 'Empresas.RUC', 'Empresas.Nombre_Comercial', 'Empresas.Razon_Social', 'Transacciones.Tipo_Documento', 'Transacciones.Clave_Acceso_SRI', 'Transacciones.Fecha_Autorizacion', 'Transacciones.Total as Valor', 'Mail_Control.Email_To', 'Mail_Control.Email_CC')
        // ->where('Mail_Control.id', '=', $id)
        //     ->get();
        return $this->find($id);

        // $param = array(
        //     'cliente' => $mail->Cliente,
        //     'logoEmpresa' =>  $mail->Patch_Logo,
        //     'nombreComercial' => $mail->Nombre_Comercial,
        //     'razonSocial' => $mail->Razon_Social,
        //     'numeroDocumento' => $mail->Numero_Documento,
        //     'valor' => $mail->Valor,
        //     'claveAcceso' => $mail->Clave_Acceso_SRI,
        //     'fechaAutorizo' => $mail->Fecha_Autorizacion,
        //     'documentoLeyenda' => SRIController::GetNombreComprobante($mail->Tipo_Documento)
        // );
    }
}
