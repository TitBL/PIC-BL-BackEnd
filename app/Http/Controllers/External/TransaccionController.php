<?php

namespace App\Http\Controllers\External;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Database\Eloquent\Collection; 
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

use App\Enums\SendType;
use App\Models\Entity\User;
use App\Models\Entity\Company;
use App\Models\Entity\Transaction;
use App\Drivers\SRIDriver;

use App\Http\Controllers\EmailNotificactionController; 

/**
 * @OA\Controller(
 * tags={"External/Transaccion"},
 * path="/external/transaccion/",
 * summary="Controlador de transacciones",
 * description="Este controlador gestiona las transacciones de sistemas externos",
 * )
 */
class TransaccionController extends ExternalController
{
    protected $CompanyModel;
    protected $UserModel;
    protected $TransaccionModel;
    public function __construct(Company $Company, User $User, Transaction $Transaction)
    {
        $this->CompanyModel = $Company;
        $this->UserModel = $User;
        $this->TransaccionModel = $Transaction;
    }

    /**
     * @OA\Post(
     *     tags={"Transaccion"},
     *     path="/external/transaccion/",  
     *     summary="Crear una nueva transaccion", 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="clave_acceso", type="string"),
     *              @OA\Property(property="monto", type="double"), 
     *              )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *     @OA\Response(
     *         response=404, 
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *     @OA\Response(
     *         response=400, 
     *         description="Bad Request",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *   security={
     *      {"Apikey": {}} 
     *      }
     * )
     */
    public function store(Request $request)
    {
        try {
            $this->validateSRIKey($request);

            $apiKey = $request->header('APIKEY');
            $clave = $request->get('clave_acceso');
            $keySplit = SRIDriver::SplitSRIkey($clave);
            $ruc = $keySplit->get('RucEmpresa');
            $comp = $this->CompanyModel->getByAPIKEY_RUC($apiKey, $ruc);

            if (!$this->TransaccionModel->findByID($clave)) {
                $document = SRIDriver::GetDocumentData($clave, $keySplit);

                $newEntity = new Transaction();
                $newEntity->key_access_sri = $clave;
                $newEntity->emission_date = Carbon::createFromFormat('dmY', $document->get('FechaEmision'));
                $newEntity->document_type = SRIDriver::GetDocumentTypeName((string) $document->get('TipoComprobante'));
                $newEntity->document_number = $document->get('Sucursal') . '-' . $document->get('PuntoEmisor') . '-' . $document->get('Secuencial');
                $newEntity->state =  $document->get('Estado');
                $newEntity->date_time_last_query = Carbon::now()->format('Y-m-d\TH:i:s.v');
                $newEntity->amount = $request->get('monto');
                $newEntity->created_at = Carbon::now()->format('Y-m-d\TH:i:s.v');
                $newEntity->emission_type = $document->get('Ambiente');

                if ($newEntity->state == "AUTORIZADO") {

                    $idEmisor = $this->UserModel->getIdForDNI($document->get('DNIReceptor'));

                    if (!$idEmisor) {
                        $idEmisor = $this->UserModel->createConsummer($document->get('DNIReceptor'), $document->get('Receptor'), $document->get('DireccionReceptor'), $document->get('EmailReceptor'));
                    }

                    $newEntity->document_issuer_id = $comp->id;
                    $newEntity->document_issuer_name = $comp->company_name;
                    $newEntity->document_receiver_id = $idEmisor->id;
                    $newEntity->document_receiver_name = $document->get('Receptor');
                    $newEntity->date_time_authorization = Carbon::parse($document->get('FechaAutorizacion'))->format('Y-m-d\TH:i:s.v');
                }
                $newEntity->save();

                $this->notificarEmail(new Collection([
                    'Reference' => $clave,
                    'ToEmail' =>  $document->get('EmailReceptor'),
                    'CCEmail' =>  '',
                    'Observaciones' =>  '',
                    'Estado' => $newEntity->state == "AUTORIZADO" ? EMAIL_SEND_STATE : EMAIL_REVIEW_TRANSACCTION
                ]));

                return $this->sendOk(SUCESS, new Collection([
                    'message' => EMAIL_TRANSACCTION_REGISTER
                ]));
            } else {
                return $this->sendOk(SUCESS, new Collection([
                    'message' => EMAIL_TRANSACCTION_EXIST
                ]));
            }
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }


    private function notificarEmail(Collection $Notificacion):void
    {
        $Not = new  EmailNotificactionController();
        $newEntity = new Collection();
        $newEntity->put('SendType', SendType::SendDocument);
        $newEntity->put('Reference', $Notificacion->get('Reference'));
        $newEntity->put('ToEmail', empty($Notificacion->get('ToEmail')) ? 'info@nn.com' : $Notificacion->get('ToEmail'));
        $newEntity->put('CCEmail', $Notificacion->get('CCEmail'));
        $newEntity->put('Observaciones', $Notificacion->get('Observaciones'));
        $newEntity->put('Estado', empty($Notificacion->get('ToEmail')) ? EMAIL_REVIEW_STATE : $Notificacion->get('Estado'));
        $Not->store($newEntity);
    }


    /**
     * Validates the request data.
     *
     * @param \Illuminate\Http\Request $request 
     * @return void
     */
    private function validateSRIKey(Request $request):void
    {
        $validator = Validator::make($request->all(), [
            'clave_acceso' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (strlen($value) !== 49) {
                        $fail('The clave must be exactly 49 characters long.');
                    }

                    $allowedCharacters = "0123456789";

                    for ($i = 0; $i < strlen($value); $i++) {
                        if (strpos($allowedCharacters, substr($value, $i, 1)) === false) {
                            $fail('The clave must consist of numbers only.');
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
