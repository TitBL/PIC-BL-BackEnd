<?php

namespace App\Http\Controllers;

use App\Exceptions\SuccessException;
use App\Models\Entity\User;
use App\Enums\SMTPSecurityType;
use App\Models\Entity\UsersTermsConditions;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Carbon\Carbon;

/**
 * The UserController class handles the logic for User entities.
 * @package App\Http\Controllers 
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class UserController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Creates a new User entity
     *
     * @param Request $request The HTTP request containing the User entity data.
     * @throws SuccessException If the creation is successful.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function store(Request $request)
    {
        $this->validateCreate($request);

        DB::beginTransaction();
        $newEntity = new User();
        $newEntity->rol_id = $request->get('IdRol');
        $newEntity->DNI = $request->get('DNI');
        $newEntity->name = $request->get('NombreUsuario');
        $newEntity->full_name = $request->get('NombreCompleto');
        $newEntity->address = $request->get('Direccion');
        $newEntity->email = $request->get('Email');
        $newEntity->password = hashPWD($request->get('Contrasena'));
        $newEntity->created_user = $request->get('IdUsuario');

        if ($newEntity->save() == 1) {
            // Insert companies 
            $companyIds = $request->get('Empresas');
            $newEntity->companies($request->get('IdUsuario'))->attach($companyIds);

            // Insert new terms and conditions
            $this->insertTerms($newEntity->id, $request->get('TerminosCondiciones'), $request->get('TerminosCondicionesAcceptacion'));

            DB::commit();
            throw new SuccessException(SUCESS_CREATION, $newEntity->findById($newEntity->id), 200);
        } else {
            DB::rollBack();
            throw new ValidationException($newEntity->errors());
        }
    }


    /**
     * Updates a User entity
     * 
     * @param  Request $request The HTTP request containing the User entity data.
     * @param  int $id The ID of the User entity.
     * @throws SuccessException If the update is successful.
     * @throws ModelNotFoundException If the User with the provided ID does not exist.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */

    public function update(Request $request, $id)
    {
        $this->validateId($id);

        DB::beginTransaction();
        // Finds the User by ID
        $updEntity = User::findOrFail($id);

        // Field validation
        $this->validateUpdate($request);

        $updEntity->rol_id = $request->get('IdRol');
        $updEntity->name = $request->get('NombreUsuario');
        $updEntity->full_name = $request->get('NombreCompleto');
        $updEntity->address = $request->get('Direccion');
        $updEntity->email = $request->get('Email');
        $updEntity->updated_user = $request->get('IdUsuario');

        if ($updEntity->save() == 1) {

            // Insert companies 
            $companyIds = $request->get('Empresas');
            $updEntity->companies($request->get('IdUsuario'))->detach();
            $updEntity->companies($request->get('IdUsuario'))->attach($companyIds);

            // Insert new terms and conditions 
            $this->insertTerms($updEntity->id, $request->get('TerminosCondiciones'), $request->get('TerminosCondicionesAcceptacion'));

            DB::commit();
            throw new SuccessException(SUCESS_UPDATE, $updEntity->findById($updEntity->id), 200);
        } else {
            DB::rollBack();
            throw new ValidationException($updEntity->errors());
        }
    }

    /**
     * Updates a new password of User 
     * 
     * @param  Request $request The HTTP request containing the User entity data.
     * @param  int $id The ID of the User entity.
     * @throws SuccessException If the update is successful.
     * @throws ModelNotFoundException If the User with the provided ID does not exist.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function update_pwd(Request $request, $id)
    {
        $this->validateId($id);

       
        // Finds the User by ID
        $updEntity = User::findOrFail($id);

        // Field validation
        $this->validateUpdatePWD($request);

        if($updEntity->password != hashPWD($request->get('PasswordAnterior'))){
            throw new ValidationException(ERROR_PASSWORD);}
        else
        {
            DB::beginTransaction();
            $updEntity->password = hashPWD($request->get('PasswordNuevo'));
            $updEntity->updated_user = $request->get('IdUsuario');

            if ($updEntity->save() == 1) {
                DB::commit();
                throw new SuccessException(SUCESS_UPDATE, $updEntity->findById($updEntity->id), 200);
            } else {
                DB::rollBack();
                throw new ValidationException($updEntity->errors());
            }
        }
    }

    /**
     * Changes the state of a User entity
     * 
     * @param  int $id The ID of the User entity.
     * @param  bool $State The state of the User entity.
     * @throws SuccessException If the state change is successful.
     * @throws ModelNotFoundException If the User with the provided ID does not exist.
     * @throws ValidationException If a validation error occurs.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function changeState($id, bool $State)
    {
        $this->validateId($id);

        // Finds the User by ID
        $updEntity = User::findOrFail($id);
        $updEntity->state = $State;
        if ($updEntity->save() == 1) {
            throw new SuccessException(SUCESS_STATE_CHANGE);
        } else {
            throw new ValidationException($updEntity->errors());
        }
    }


    /**
     * Insert new terms and conditions for terms of user.
     *
     * @param int $user_id The ID of the user for whom terms and conditions are being inserted.
     * @param string|null $details The details/terms accepted by the user. Set to null if not provided.
     * @param bool|null $acceptance The acceptance status of the terms. Set to null if not provided.
     *
     * @return UsersTermsConditions The newly created terms and conditions entity.
     *
     * @throws ValidationException If there is an issue with the validation of the new terms and conditions entity.
     */
    protected function insertTerms($user_id, $details, $acceptance)
    {
        $newTermEntity = new UsersTermsConditions();
        $newTermEntity->user_id = $user_id;
        $newTermEntity->created_at = Carbon::now()->format('Y-m-d\TH:i:s.v');
        if (!empty($details)) {
            $newTermEntity->details_terms = $details;
            $newTermEntity->terms_accept = $acceptance;
        }
        if ($newTermEntity->save()) {
            return $newTermEntity;
        } else {
            throw new ValidationException($newTermEntity->errors());
        }
    }


    /**
     * Validates input data for the creation of a User entity.
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
            'DNI' => ['required', 'max:100', Rule::unique('users', 'DNI')],
            'NombreUsuario' => ['required', 'max:50', Rule::unique('users', 'name')],
            'NombreCompleto' => ['required', 'max:200'],
            'IdRol' => ['required', 'integer'],
            'Contrasena' => ['required', 'max:16'],
            'Email' => ['required', 'max:225', 'email', Rule::unique('users', 'email')],
            'Direccion' => ['max:225'],
            'IdUsuario' => ['required', 'integer'],
            'TerminosCondiciones' => ['required', 'max:255'],
            'TerminosCondicionesAcceptacion' => ['required', 'boolean'],
            'Empresas' => ['array'],
            'Empresas.*' => ['integer'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validates input data for the update of a User entity.
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
            'NombreUsuario' => ['required', 'max:50', Rule::unique('users', 'name')],
            'NombreCompleto' => ['required', 'max:200'],
            'IdRol' => ['required', 'integer'],
            'Email' => ['required', 'max:225', 'email', Rule::unique('users', 'email')],
            'Direccion' => ['max:225'],
            'IdUsuario' => ['required', 'integer'],
            'TerminosCondiciones' => ['max:255'],
            'TerminosCondicionesAcceptacion' => ['boolean'],
            'Empresas' => ['array'],
            'Empresas.*' => ['integer'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validates input data for the pwd update of a User.
     *
     * @param Request $request The HTTP request containing the data to validate.
     * @throws ValidationException If validation fails.
     *
     * @return void
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function validateUpdatePWD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'PasswordAnterior' => ['required', 'max:200'],
            'PasswordNuevo' => ['required', 'max:200'],
            'IdUsuario' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateId($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'not_in:0'],
            [
                'id.not_in' => 'The id cannot be modified.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
