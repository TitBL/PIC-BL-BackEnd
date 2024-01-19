<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\UserController;
use App\Exceptions\SuccessException;
use App\Models\Entity\User;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

/**
 * @OA\Controller(
 *   tags={"Usuario"},
 *   path="/api/usuario/",
 *   summary="Controlador de Usuarios",
 *   description="Este controlador gestiona las Usuarios de la API",
 * )
 */
class UsuarioController extends ApiController
{
    protected $UserModel;
    public function __construct(User $User)
    {
        $this->UserModel = $User;
    }

    /**
     * @OA\Get(
     *   tags={"Usuario"},
     *   path="/api/usuario/list/{estado}",
     *   summary="Obtiene una lista de usuarios por estado.",
     *   @OA\Parameter(
     *         name="estado",
     *         in="path",
     *         required=true,
     *         description="Filtra el usuario por estado.",
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

            $results = $this->UserModel->getByState($estadoBool);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Usuario"},
     *   path="/api/usuario/buscar/{filtro}",
     *   summary="Obtiene una lista de Usuarios por busqueda.",
     *   @OA\Parameter(
     *         name="filtro",
     *         in="path",
     *         required=true,
     *         description="Filtra la Usuario por ruc, razon_social, nombre_comercial.",
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
    public function showAllbyFilter($filtro)
    {
        try {
            $results = $this->UserModel->getByFilter($filtro);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Usuario"},
     *   path="/api/usuario/perfil/",
     *   summary="Obtiene el perfil del usuario de session.",
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
    public function showPerfil(Request $request)
    {
        try {
            $user = User::find($request->get('IdUsuario'));
            $results = $this->UserModel->castLoginEntity($user);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Usuario"},
     *     path="/api/usuario/{id}",
     *     summary="Obtener detalles de un usuario por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de usuario",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrada"
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
            $results = $this->UserModel->findById($id);
            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Usuario"},
     *     path="/api/usuario/",
     *     summary="Crea un nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="DNI", type="string"),
     *              @OA\Property(property="NombreUsuario", type="string"),
     *              @OA\Property(property="NombreCompleto", type="string"),
     *              @OA\Property(property="IdRol", type="int"),
     *              @OA\Property(property="Contrasena", type="string"),
     *              @OA\Property(property="Email", type="string"),
     *              @OA\Property(property="Direccion", type="string"),
     *              @OA\Property(property="IdUsuario", type="int"),
     *              @OA\Property(property="TerminosCondiciones", type="string"),
     *              @OA\Property(property="TerminosCondicionesAcceptacion", type="bool", example=0),
     *              @OA\Property(property="Empresas", type="array", @OA\Items(type="integer"))
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
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function store(Request $request)
    {
        try {
            $Controller = new UserController();
            $Controller->store($request);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage(), $e->getResul(), $e->getCode());
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Usuario"},
     *     path="/api/usuario/{id}",
     *     summary="Actualiza la entidad usuario",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la usuario a actualizar.",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="NombreUsuario", type="string"),
     *              @OA\Property(property="NombreCompleto", type="string"),
     *              @OA\Property(property="IdRol", type="int"),
     *              @OA\Property(property="Email", type="string"),
     *              @OA\Property(property="Direccion", type="string"),
     *              @OA\Property(property="IdUsuario", type="int"),
     *              @OA\Property(property="TerminosCondiciones", type="string"),
     *              @OA\Property(property="TerminosCondicionesAcceptacion", type="bool", example=0),
     *              @OA\Property(property="Empresas", type="array", @OA\Items(type="integer"))
     *              )
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
            $Controller = new UserController();
            $Controller->update($request, $id);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Usuario"},
     *     path="/api/usuario/cambiarpwd/{id}",
     *     summary="cambia la contraseña de la entidad usuario",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la usuario a actualizar.",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="PasswordAnterior", type="string"),
     *              @OA\Property(property="PasswordNuevo", type="string"), 
     *              @OA\Property(property="IdUsuario", type="int"), 
     *              )
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
    public function updatePassword(Request $request, $id)
    {
        try {
            $Controller = new UserController();
            $Controller->update_pwd($request, $id);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Usuario"},
     *     path="/api/usuario/deshabilitar/{id}",
     *     summary="Deshabilita una entidad usuario",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la usuario a deshabilitar.",
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
            $Controller = new UserController();
            $Controller->changeState($id, false);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Usuario"},
     *     path="/api/usuario/habilitar/{id}",
     *     summary="Habilita una entidad usuario",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la usuario a habilitar.",
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
            $Controller = new UserController();
            $Controller->changeState($id, true);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }
}
