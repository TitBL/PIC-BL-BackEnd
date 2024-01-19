<?php

namespace App\Http\Controllers\Api;

use App\Models\View\DocPatchFile;
use App\Models\View\DocIssuerReceived;
use App\Models\View\DocCompanyReceived;
use App\Models\View\DocCompanyIssuer;
use App\Drivers\StorageDriver;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Exception;

/** 
 * @OA\Controller(
 *   tags={"Documento"},
 *   path="/api/documento/",
 *   summary="Controlador de Documentos",
 *   description="Este controlador gestiona los Documentos de la API",
 * ),
 */
class DocumentoController extends ApiController
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *   tags={"Documento"},
     *   path="/api/documento/list/recibidos/consumidor",
     *   summary="Obtiene una lista de documentos recibidos para el consumidor.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *              @OA\Property(property="IdUsuario", type="int"),
     *              @OA\Property(property="FechaDesde", type="date"),
     *              @OA\Property(property="FechaHasta", type="date")
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
    public function showAllDocByConsumerRecivedFilter(Request $request)
    {
        try {
            $this->validateDateRange($request);

            $id = $request->input('IdUsuario');
            $startDate = $request->input('FechaDesde');
            $endDate = $request->input('FechaHasta');
            $results = DocIssuerReceived::getListByUserAndDateRange($id, $startDate, $endDate);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Documento"},
     *   path="/api/documento/list/emitidos/empresa",
     *   summary="Obtiene una lista de documentos emitidos por la empresa.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *              @OA\Property(property="IdEmpresa", type="int"),
     *              @OA\Property(property="FechaDesde", type="date"),
     *              @OA\Property(property="FechaHasta", type="date")
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
    public function showAllDocByCompanyIssuerFilter(Request $request)
    {
        try {
            $this->validateDateRange($request);
            $this->validateIdEmpresa($request);

            $id = $request->input('IdEmpresa');
            $startDate = $request->input('FechaDesde');
            $endDate = $request->input('FechaHasta');
            $results = DocCompanyIssuer::getListByUserAndDateRange($id, $startDate, $endDate);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *   tags={"Documento"},
     *   path="/api/documento/list/recibidos/empresa",
     *   summary="Obtiene una lista de documentos recibidos para la empresa.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *              @OA\Property(property="IdEmpresa", type="int"),
     *              @OA\Property(property="FechaDesde", type="date"),
     *              @OA\Property(property="FechaHasta", type="date")
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
    public function showAllDocByCompanyReceivedFilter(Request $request)
    {
        try {
            $this->validateDateRange($request);
            $this->validateIdEmpresa($request);

            $id = $request->input('IdEmpresa');
            $startDate = $request->input('FechaDesde');
            $endDate = $request->input('FechaHasta');
            $results = DocCompanyReceived::getListByUserAndDateRange($id, $startDate, $endDate);

            return $this->sendOk(SUCESS, $results);
        } catch (ValidationException $e) {
            return $this->sendError($e->validator->errors());
        } catch (\Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Documento"},
     *     path="/api/documento/visualizar/pdf/{id}", 
     *     summary="Visualizar PDF",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de transaccion, clave de acceso",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la Documento",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Documento no encontrada"
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function show_pdf(string $id)
    {
        try {
            $file = $this->getFilePDF($id);

            return $this->sendOk_file($file, "application/pdf");
        } catch (ModelNotFoundException $e) {
            throw $this->sendException(ERROR_NOT_FOUD, $e);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Documento"},
     *     path="/api/documento/descargar/pdf/{id}", 
     *     summary="Desacargar PDF",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de transaccion, clave de acceso",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la Documento",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Documento no encontrada"
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function download_pdf(string $id)
    {
        try {
            $file = $this->getFilePDF($id);
            return $this->sendOk_file($file, "application/pdf", $id . ".pdf");
        } catch (ModelNotFoundException $e) {
            throw $this->sendException(ERROR_NOT_FOUD, $e);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Documento"},
     *     path="/api/documento/visualizar/xml/{id}", 
     *     summary="Obtener detalles de una Documento por ID",     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de transaccion, clave de acceso ",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la Documento",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Documento no encontrada"
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function show_xml($id)
    {
        try {
            $file = $this->getFileXML($id);
            return $this->sendOk_file($file, 'application/xml');
        } catch (ModelNotFoundException $e) {
            throw $this->sendException(ERROR_NOT_FOUD, $e);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Documento"},
     *     path="/api/documento/descargar/xml/{id}", 
     *     summary="Desacargar xml",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de transaccion, clave de acceso",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la Documento",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Documento no encontrada"
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function download_xml(string $id)
    {
        try {
            $file = $this->getFileXML($id);
            return $this->sendOk_file($file, "application/xml", $id . ".xml");
        } catch (ModelNotFoundException $e) {
            throw $this->sendException(ERROR_NOT_FOUD, $e);
        } catch (Exception $e) {
            return $this->sendException($e->getMessage(), $e);
        }
    }

    /**
     * Obtiene la ruta del archivo PDF según el ID proporcionado.
     *
     * @param int $id Identificador único del documento.
     * @return string Ruta del archivo PDF.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function getFilePDF($id)
    {
        $comp = $this->getFilePatch($id, 'PDF_Patch');
        return StorageDriver::GetFile($comp->PDF_Patch);
    }

    /**
     * Obtiene la ruta del archivo XML según el ID proporcionado.
     *
     * @param int $id Identificador único del documento.
     * @return string Ruta del archivo XML.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function getFileXML($id)
    {
        $comp = $this->getFilePatch($id, 'XML_Patch');
        return StorageDriver::GetFile($comp->XML_Patch);
    }

    /**
     * Obtiene la ruta del archivo según el ID y el campo especificado.
     *
     * @param int $id Identificador único del documento.
     * @param string $campo Nombre del campo que contiene la ruta del archivo.
     * @return stdClass Objeto que contiene la ruta del archivo.
     * @throws ModelNotFoundException Excepción si no se encuentra el registro.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function getFilePatch($id, $campo)
    {
        return DocPatchFile::select($campo)
            ->where('SRI_Key', '=', $id)
            ->firstOrFail();
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
    private function validateDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'FechaDesde' => ['required', 'date', 'before:tomorrow'],
            'FechaHasta' => ['required', 'date', 'after:FechaDesde'],
        ], [
            'FechaHasta.after' => 'The value of the FechaHasta must be later than FechaDesde.',
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
    private function validateIdEmpresa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdEmpresa' => ['required', 'int'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
