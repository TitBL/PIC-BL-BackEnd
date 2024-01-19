<?php

namespace App\Http\Controllers;

use App\Drivers\StorageDriver;
use App\Mail\DocumentoElectronicoMail;
use App\Models\Entity\EmailNotification;
use App\Models\View\DocEmailPendingSend;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

/**
 * The EmailNotficactionController class handles the logic for Email Notificaction entities.
 * @package App\Http\Controllers 
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class EmailNotificactionController extends Controller
{
    public function store(Collection $Notificacion)
    {
        EmailNotification::insert([
            'send_type' => $Notificacion->get('SendType'),
            'reference' => $Notificacion->get('Reference'),
            'send_to' => $Notificacion->get('ToEmail'),
            'sent_cc' => $Notificacion->get('CCEmail'),
            'observations' => $Notificacion->get('Observaciones'),
            'state' => $Notificacion->get('Estado'),
            'created_at' => Carbon::now()->format('Y-m-d\TH:i:s.v')
        ]);
    }

    public function reviewPendingToSubmit()
    {
        foreach (DocEmailPendingSend::all() as $emailP) {
        }
    }

    // private function sendMail(Collection $transaccion)
    // {
    //     $EmailNotificationModel = new EmailNotification();
    //     $EmailNotificationModel->saveStatus($transaccion->id, EMAIL_SEND_PREPARING);
    //     try {
    //         $mailParam = $EmailNotificationModel->getMailParam($TransactionId)->all()[0];

    //         $param = array(
    //             'cliente' => $mailParam->Cliente,
    //             'logoEmpresa' =>  $mailParam->Patch_Logo,
    //             'nombreComercial' => $mailParam->Nombre_Comercial,
    //             'razonSocial' => $mailParam->Razon_Social,
    //             'numeroDocumento' => $mailParam->Numero_Documento,
    //             'valor' => $mailParam->Valor,
    //             'claveAcceso' => $mailParam->Clave_Acceso_SRI,
    //             'fechaAutorizo' => $mailParam->Fecha_Autorizacion,
    //             // 'documentoLeyenda' => SRIController::GetNombreComprobante($mailParam->Tipo_Documento)
    //         );
    //         $MyMail = new DocumentoElectronicoMail($param, 'Facturación ' . $mailParam->Nombre_Comercial, 'Documento Electrónico #' . $mailParam->Numero_Documento);

    //         $key = $mailParam->Clave_Acceso_SRI;
    //         $File = $mailParam->Patch_Folder .  substr($key, 4, 4) . '/' . substr($key, 2, 2) . '/' . substr($key, 0, 2) . '/' . $mailParam->Clave_Acceso_SRI;

    //         if (StorageDriver::FileExists('local', $File . '.pdf')) {
    //             $MyMail->attachFromStorageDisk('local', $File . '.pdf');
    //         }

    //         if (StorageDriver::FileExists('local', $File . '.xml')) {
    //             $MyMail->attachFromStorageDisk('local', $File . '.xml');
    //         }

    //         //  Mail::to($mail->Email_To, $mail->Cliente)->bcc('soporte@azulser.com')->send($MyMail);
    //         Mail::to($mailParam->Email_To, $mailParam->Cliente)->send($MyMail);
    //         $EmailNotificationModel->saveStatus($TransactionId, EMAIL_SEND);
    //     } catch (\Exception $th) {
    //         $EmailNotificationModel->saveStatus($TransactionId, EMAIL_SEND, $th->getMessage());
    //     }
    // }
}
