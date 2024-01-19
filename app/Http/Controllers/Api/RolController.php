<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CompanyController;
use App\Exceptions\SuccessException;
use App\Enums\Permissions;
use App\Models\Entity\Rol;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Controller(
 *   tags={"Rol"},
 *   path="/api/rol/",
 *   summary="Controlador de roles",
 *   description="Este controlador gestiona los roles de la API",
 * )
 */
class RolController extends ApiController
{
    protected $RolModel;
    public function __construct(Rol $Rol)
    {
        $this->RolModel = $Rol;
    }

    /**
     * @OA\Get(
     *   tags={"Rol"},
     *   path="/api/rol/list/{estado}",
     *   summary="Obtiene una lista de roles por estado.",

     *   @OA\Parameter(
     *         name="estado",
     *         in="path",
     *         required=true,
     *         description="Filtra el rol por estado.",
     *         @OA\Schema(type="boolean")
     *   ), 
     *   @OA\Response(
     *     response=400, 
     *     description="Error",
     *     @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *   ),
     *  @OA\Response(
     *     response=200, 
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *   ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function showAllbyState($estado)
    {
        try {
            $estadoBool = filter_var($estado, FILTER_VALIDATE_BOOLEAN);

            $results = $this->RolModel->getByState($estadoBool);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        }
    }

    /**
     * @OA\Get(
     *   tags={"Rol"},
     *   path="/api/rol/buscar/{nombre} ",
     *   summary="Obtiene una lista de roles por busqueda de nombre.",
     *       
     *   @OA\Parameter(
     *         name="nombre",
     *         in="path",
     *         required=true,
     *         description="Realiza una busqueda por el nombre del rol.",
     *         @OA\Schema(type="string")
     *   ), 
     *   @OA\Response(
     *     response=400, 
     *     description="Error",
     *     @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *   ),
     *  @OA\Response(
     *     response=200, 
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *   ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function showAllbyName($nombre)
    {
        try {
            $results = $this->RolModel->getByFilter($nombre);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        }
    }

    /**
     * @OA\Get(
     *     tags={"Rol"},
     *     path="/api/rol/{id}", 
     *     summary="Obtener detalles de un rol por ID",
     *         
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la rol",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="rol no encontrada"
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function show($id)
    {
        try {
            $results =  $this->RolModel->findByID($id);
            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }


    /**
     * @OA\Get(
     *   tags={"Rol"},
     *   path="/api/rol/permisos/",
     *   summary="Listado de permisos",
     *       
     *   @OA\Response(
     *         response=200,
     *         description="Listado detalle de permiso",
     *     ),
     *   @OA\Response(
     *         response=404,
     *         description="Permiso no encontrada"
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function getListPermission()
    {
        $permissions = Permissions::getAllPermissions();
        return $this->sendOk(SUCESS,  $permissions);
    }

    /**
     * @OA\Post(
     *     tags={"Rol"},
     *     path="/api/rol/",  
     *     summary="Crear una nuevo rol",
     *         
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="Nombre", type="string"),
     *              @OA\Property(property="Descripcion", type="string"),
     *              @OA\Property(property="Permisos", type="array", @OA\Items(type="integer")),        
     *              @OA\Property(property="IdUsuario", type="int")
     *        )
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
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function store(Request $request)
    {
        try {
            // validación de campos
            $this->validateRol($request);

            // Iniciar transacción
            DB::beginTransaction();
            $newEntity = new Rol();
            $newEntity->name = $request->get('Nombre');
            $newEntity->description = $request->get('Descripcion');
            $newEntity->created_user = $request->get('IdUsuario');
            $newEntity->state = true;
            if ($newEntity->save() == 1) {
                // Asociar permisos al nuevo rol
                $permisosIds = $request->get('Permisos');
                $newEntity->permissions($request->get('IdUsuario'))->attach($permisosIds);
                // Confirmar la transacción
                DB::commit();

                return $this->sendOk(SUCESS_CREATION, $newEntity, 201);
            } else {
                // Rollback en caso de error
                DB::rollBack();
                throw new ValidationException($newEntity->errors());
            }
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (\Exception $e) {
            // Rollback en caso de error general
            DB::rollBack();
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Rol"},
     *     path="/api/rol/{id}",  
     *     summary="Actualiza la entidad rol",         
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de rol a actualizar.",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="Nombre", type="string"),
     *              @OA\Property(property="Descripcion", type="string"),
     *              @OA\Property(property="Permisos", type="array", @OA\Items(type="integer")),        
     *              @OA\Property(property="IdUsuario", type="int")
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
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
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function update(Request $request, $id)
    {
        try {

            DB::beginTransaction();
            $this->validateId($id);

            $updEntity = Rol::findOrFail($id);

            $this->validateRol($request);
            $updEntity->name = $request->get('Nombre');
            $updEntity->description = $request->get('Descripcion');
            $updEntity->updated_user = $request->get('IdUsuario');

            if ($updEntity->save() == 1) {
                $permisosIds = $request->get('Permisos');
                $updEntity->permissions($request->get('IdUsuario'))->detach();
                $updEntity->permissions($request->get('IdUsuario'))->attach($permisosIds);
                DB::commit();

                return $this->sendOk(SUCESS_UPDATE);
            } else {
                // Rollback en caso de error
                DB::rollBack();
                throw new ValidationException($updEntity->errors());
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Rol"},
     *     path="/api/rol/deshabilitar/{id}",  
     *     summary="Deshabilita una entidad rol",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la rol a deshabilitar.",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
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
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function disable($id)
    {
        try {
            $this->validateId($id);
            $this->changeState($id, false);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Rol"},
     *     path="/api/rol/habilitar/{id}",  
     *     summary="Habilita una entidad rol",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la rol a habilitar.",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
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
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function enable($id)
    {
        try {
            $this->validateId($id);
            $this->changeState($id, true);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }


    /**
     * Cambia de estado de una entidad Rol
     * 
     * @param  $id de entidad Rol.
     * @param  bool $Estado de entidad Rol.
     * @throws SuccessException Si la creación es exitosa.
     * @throws ModelNotFoundException Si la Rol con el ID proporcionado no existe.
     * @throws ValidationException Si ocurre un error de validación.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function changeState($id, bool $Estado)
    {
        // Busca la rol por ID
        $updEntity = Rol::findOrFail($id);
        $updEntity->state = $Estado;
        if ($updEntity->save() == 1) {
            throw new SuccessException(SUCESS_STATE_CHANGE);
        } else {
            throw new ValidationException($updEntity->errors());
        }
    }

    /**
     * Valida los datos de entrada para la creación de una entidad rol.
     *
     * @param Request $request La solicitud HTTP que contiene los datos a validar.
     * @throws ValidationException Si la validación falla.
     *
     * @return void
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function validateRol(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nombre' => ['required', 'max:20', Rule::unique('roles', 'name')],
            'Descripcion' => 'max:100',
            'Permisos' => 'required|max:100',
            'IdUsuario' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateId($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'not_in:0,1'],
            [
                'id.not_in' => 'The id cannot be modified.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
