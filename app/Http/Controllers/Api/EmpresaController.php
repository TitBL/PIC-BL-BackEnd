<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CompanyController;
use App\Exceptions\SuccessException;
use App\Models\Entity\Company;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

/**
 *
 * @OA\Tag(
 *     name="Empresa", 
 * ),
 *
 * @OA\Controller(
 *   tags={"Empresa"},
 *   path="/api/empresa/",
 *   summary="Controlador de empresas",
 *   description="Este controlador gestiona las empresas de la API",
 * ),
 */
class EmpresaController extends ApiController
{
    protected $CompanyModel;
    public function __construct(Company $Company)
    {
        $this->CompanyModel = $Company;
    }

    /**
     * @OA\Get(
     *   tags={"Empresa"},
     *   path="/api/empresa/list/{estado}",
     *   summary="Obtiene una lista de empresas por estado.", 
     *   @OA\Parameter(
     *         name="estado",
     *         in="path",
     *         required=true,
     *         description="Filtra la empresa por estado.",
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

            $results = $this->CompanyModel->getByState($estadoBool);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Empresa"},
     *   path="/api/empresa/api_key",
     *   summary="Obtiene una lista de empresas por busqueda.",
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
    public function getApiKEY()
    {
        try {
            $valor = $this->CompanyModel->generarAPIKey();
            return $this->sendOk(SUCESS, new Collection(['ApiKey' => $valor]), 200);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Empresa"},
     *   path="/api/empresa/buscar/{filtro}",
     *   summary="Obtiene una lista de empresas por busqueda.",

     *   @OA\Parameter(
     *         name="filtro",
     *         in="path",
     *         required=true,
     *         description="Filtra la empresa por ruc, razon_social, nombre_comercial.",
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
            $results = $this->CompanyModel->getByFilter($filtro);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Empresa"},
     *     path="/api/empresa/{id}", 
     *     summary="Obtener detalles de una empresa por ID",
     *     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la empresa",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
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
            $results = Company::select(
                'id',
                'patch_logo as Logo',
                'RUC',
                'company_name as RazonSocial',
                'commercial_name as NombreComercial',
                'can_send_email as PuedeEnviarCorreo',
                'can_used_smtp as UsaConfiguracionSMTP',
                'smtp_email as Email',
                'smtp_server as Servidor',
                'smtp_port as Puerto',
                'smtp_type_security as TipoSeguridad',
                'smtp_user as UsuarioSMTP',
                'smtp_password as PasswordSMTP',
                'api_key as APIKey',
                'patch_logo as Logo'
            )
                ->where('state', '=', true)
                ->findOrFail($id);
            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Empresa"},
     *     path="/api/empresa/",  
     *     summary="Crear una nueva empresa",
     *     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="RUC", type="string"),
     *              @OA\Property(property="RazonSocial", type="string"),
     *              @OA\Property(property="NombreComercial", type="string"),
     *              @OA\Property(property="PuedeEnviarCorreo", type="bool", example=0),
     *              @OA\Property(property="UsaConfiguracionSMTP", type="bool" , example=0),
     *              @OA\Property(property="Email", type="string"),
     *              @OA\Property(property="Servidor", type="string"),
     *              @OA\Property(property="Puerto", type="int"),
     *              @OA\Property(property="TipoSeguridad", type="int", example=0),
     *              @OA\Property(property="UsuarioSMTP", type="string"),
     *              @OA\Property(property="PasswordSMTP", type="string"),
     *              @OA\Property(property="APIKey", type="string"),
     *              @OA\Property(property="Logo", type="string"),
     *              @OA\Property(property="IdUsuario", type="int")
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
            $Controller = new CompanyController();
            $Controller->store($request);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage(), $e->getResul(), $e->getCode());
        } catch (ValidationException $e) {
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Empresa"},
     *     path="/api/empresa/{id}",  
     *     summary="Actualiza la entidad empresa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la empresa a actualizar.",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *              @OA\Property(property="NombreComercial", type="string"),
     *              @OA\Property(property="PuedeEnviarCorreo", type="bool", example=0),
     *              @OA\Property(property="UsaConfiguracionSMTP", type="bool" , example=0),
     *              @OA\Property(property="Email", type="string"),
     *              @OA\Property(property="Servidor", type="string"),
     *              @OA\Property(property="Puerto", type="int"),
     *              @OA\Property(property="TipoSeguridad", type="int", example=0),
     *              @OA\Property(property="UsuarioSMTP", type="string"),
     *              @OA\Property(property="PasswordSMTP", type="string"),
     *              @OA\Property(property="APIKey", type="string"),
     *              @OA\Property(property="Logo", type="string"),
     *              @OA\Property(property="IdUsuario", type="int")
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
            $Controller = new CompanyController();
            $Controller->update($request, $id);
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
     *     tags={"Empresa"},
     *     path="/api/empresa/deshabilitar/{id}",  
     *     summary="Deshabilita una entidad empresa",
     *     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la empresa a deshabilitar.",
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
            $Controller = new CompanyController();
            $Controller->changeState($id, false);
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
     *     tags={"Empresa"},
     *     path="/api/empresa/habilitar/{id}",  
     *     summary="Habilita una entidad empresa",
     *     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la empresa a habilitar.",
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
            $Controller = new CompanyController();
            $Controller->changeState($id, true);
        } catch (SuccessException $e) {
            return $this->sendOk($e->getMessage());
        } catch (ModelNotFoundException $e) {
            return $this->sendError(ERROR_NOT_FOUD);
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }
}
