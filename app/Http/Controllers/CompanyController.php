<?php

namespace App\Http\Controllers;

use BenSampo\Enum\Rules\EnumValue;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Exceptions\SuccessException;
use App\Drivers\StorageDriver;
use App\Models\Entity\Company;
use App\Enums\SMTPSecurityType;

/**
 * The CompanyController class handles the logic for Company entities.
 * @package App\Http\Controllers 
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class CompanyController extends Controller
{

    /**
     * Creates a new Company entity
     *
     * @param Request $request The HTTP request containing the Company entity data.
     * @throws SuccessException If the creation is successful.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function store(Request $request)
    {
        // validación de campos
        $this->validateCreate($request);

        $newEntity = new Company();
        $newEntity->RUC = $request->get('RUC');
        $newEntity->company_name = $request->get('RazonSocial');
        $newEntity->commercial_name = $request->get('NombreComercial');
        $newEntity->can_send_email = $request->get('PuedeEnviarCorreo');
        $newEntity->can_used_smtp = $request->get('UsaConfiguracionSMTP');
        $newEntity->smtp_email = $request->get('Email');
        $newEntity->smtp_server = $request->get('Servidor');
        $newEntity->smtp_port = $request->get('Puerto');
        $newEntity->smtp_type_security = $request->get('TipoSeguridad');
        $newEntity->smtp_user = $request->get('UsuarioSMTP');
        $newEntity->smtp_password = $request->get('PasswordSMTP');
        $newEntity->api_key = $request->get('APIKey');
        $newEntity->patch_folder = StorageDriver::GetCompanyFolder($request->get('RUC'));
        // $newEntity->patch_logo = $request->get('Logo');
        $newEntity->patch_logo = '/logo/';
        $newEntity->created_user = $request->get('IdUsuario');

        if ($newEntity->save() == 1) {
            // Crea el repositorio
            StorageDriver::CreateDirectory($newEntity->patch_folder);
            // retorno de entidad
            $response = $newEntity->findByRUC($request->get('RUC'));
            throw new SuccessException(SUCESS_CREATION, $response, 201);
        } else {
            throw new ValidationException($newEntity->errors());
        }
    }


    /**
     * Updates a Company entity
     * 
     * @param  Request $request The HTTP request containing the Company entity data.
     * @param  int $id The ID of the Company entity.
     * @throws SuccessException If the update is successful.
     * @throws ModelNotFoundException If the Company with the provided ID does not exist.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function update(Request $request, $id)
    {

        // Finds the company by ID
        $updEntity = Company::findOrFail($id);

        // Field validation
        $this->validateUpdate($request);

        $updEntity->commercial_name = $request->get('NombreComercial');
        $updEntity->can_send_email = $request->get('PuedeEnviarCorreo');
        $updEntity->can_used_smtp = $request->get('UsaConfiguracionSMTP');
        $updEntity->smtp_email = $request->get('Email');
        $updEntity->smtp_server = $request->get('Servidor');
        $updEntity->smtp_port = $request->get('Puerto');
        $updEntity->smtp_type_security = $request->get('TipoSeguridad');
        $updEntity->smtp_user = $request->get('UsuarioSMTP');
        $updEntity->smtp_password = $request->get('PasswordSMTP');
        $updEntity->api_key = $request->get('APIKey');
        $updEntity->updated_user = $request->get('IdUsuario');

        if ($updEntity->save() == 1) {
            throw new SuccessException(SUCESS_UPDATE);
        } else {
            throw new ValidationException($updEntity->errors());
        }
    }

    /**
     * Changes the state of a Company entity
     * 
     * @param  int $id The ID of the Company entity.
     * @param  bool $estado The state of the Company entity.
     * @throws SuccessException If the state change is successful.
     * @throws ModelNotFoundException If the Company with the provided ID does not exist.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function changeState($id, bool $Estado)
    {
        // Finds the company by ID
        $updEntity = Company::findOrFail($id);
        $updEntity->state = $Estado;
        if ($updEntity->save() == 1) {
            throw new SuccessException(SUCESS_STATE_CHANGE);
        } else {
            throw new ValidationException($updEntity->errors());
        }
    }

    /**
     * Validates input data for the creation of a Company entity.
     *
     * @param Request $request The HTTP request containing the data to validate.
     * @throws ValidationException If validation fails.
     *
     * @return void
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function validateCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'RUC' => ['required', 'max:20', Rule::unique('companies', 'RUC')],
            'RazonSocial' =>'required', 'max:100',
            'NombreComercial' =>'required', 'max:100',
            'PuedeEnviarCorreo' =>'required', 'boolean',
            'UsaConfiguracionSMTP' =>'required', 'boolean',
            'Email' => [$request->input('UsaConfiguracionSMTP') ? 'max:225' : 'required', 'max:225', 'email'],
            'Servidor' => $request->input('UsaConfiguracionSMTP') ? 'max:200' :'required', 'max:200',
            'Puerto' => $request->input('UsaConfiguracionSMTP') ? 'integer' :'required', 'integer',
            'TipoSeguridad' => $request->input('UsaConfiguracionSMTP') ? [new EnumValue(SMTPSecurityType::class)] : ['required', new EnumValue(SMTPSecurityType::class)],
            'UsuarioSMTP' => $request->input('UsaConfiguracionSMTP') ? 'max:200' :'required', 'max:200',
            'PasswordSMTP' => $request->input('UsaConfiguracionSMTP') ? 'max:200' :'required', 'max:200',
            'APIKey' =>'required', 'max:255',
            'Logo' => 'max:255',
            'IdUsuario' =>'required', 'integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validates input data for the update of a Company entity.
     *
     * @param Request $request The HTTP request containing the data to validate.
     * @throws ValidationException If validation fails.
     *
     * @return void
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function validateUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NombreComercial' => 'required|max:100',
            'PuedeEnviarCorreo' => 'required|boolean',
            'UsaConfiguracionSMTP' => 'required|boolean',
            'Email' => [$request->input('UsaConfiguracionSMTP') ? 'max:225' : 'required|max:225', 'email'],
            'Servidor' => $request->input('UsaConfiguracionSMTP') ? 'max:200' : 'required|max:200',
            'Puerto' => $request->input('UsaConfiguracionSMTP') ? 'integer' : 'required|integer',
            'TipoSeguridad' => $request->input('UsaConfiguracionSMTP') ? [new EnumValue(SMTPSecurityType::class)] : ['required', new EnumValue(SMTPSecurityType::class)],
            'UsuarioSMTP' => $request->input('UsaConfiguracionSMTP') ? 'max:200' : 'required|max:200',
            'PasswordSMTP' => $request->input('UsaConfiguracionSMTP') ? 'max:200' : 'required|max:200',
            'APIKey' => 'required|max:255',
            'Logo' => 'max:255',
            'IdUsuario' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
