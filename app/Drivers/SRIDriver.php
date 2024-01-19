<?php

namespace App\Drivers;

use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * Static class for managing interactions with SRI services.
 *
 * @package App\Drivers
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class SRIDriver
{
    /**
     * Get document data using the SRI key.
     *
     * @param  String  $SRI_key SRI Access/Authorization Key.
     * @return Collection|null Document data Collection or null if not authorized.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public static function GetDocumentData(String $SRI_key, Collection $data)
    {
        $SRI = new SRIDriver();
        try {
            $xml = $SRI->getDocument($SRI_key);
        } catch (\Throwable $th) {
            $data->put("Ambiente",  DEFAULT_AMBIENTE);
            $data->put("Estado",  ERROR_QUERY_SRI);
            return  $data;
        }


        if ($xml->numeroComprobantes == 0) {
            return  $data;
        }

        $authorization = $xml->autorizaciones[0]->autorizacion;
        if ($SRI->getDocumentAuthorization($authorization, $data)) {
            $xmlComprobante = simplexml_load_string((string)$authorization->comprobante, null);

            $pathCompany = StorageDriver::GetCompanyFolder($data->get('RucEmpresa'));
            $directory = $pathCompany . $data->get('AnioEmision') . '/' . $data->get('MesEmision') . '/' . $data->get('DiaEmision'). '/' ;
            $file =   $directory.$SRI_key . '.xml';
            StorageDriver::SaveXML($xmlComprobante, $file);
            return $SRI->getDocumentValue($xmlComprobante, $data);
        }
        return $data;
    }


    /**
     * Convert an SRI Access/Authorization Key for division into fields.
     *
     * @param  String  $key SRI Access/Authorization Key.
     * @return Collection Collection with key details if invalid.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    static function SplitSRIkey(String $key = null)
    {
        if (!isset($key) || strlen($key) != 49) {
            throw new \InvalidArgumentException("Key cannot be empty");
        }

        return new Collection([
            "DiaEmision" => substr($key, 0, 2),
            "MesEmision" => substr($key, 2, 2),
            "AnioEmision" => substr($key, 4, 4),
            "FechaEmision" => substr($key, 0, 8),
            "TipoComprobante" => substr($key, 8, 2),
            "RucEmpresa" => substr($key, 10, 13),
            "Ambiente" => substr($key, 23, 1),
            "Sucursal" => substr($key, 24, 3),
            "PuntoEmisor" => substr($key, 27, 3),
            "Secuencial" => substr($key, 30, 9)
        ]);
    }

    /**
     * Get the name of the document type based on the SRI code.
     *
     * @param String $DocumentType SRI Document Type Code.
     * @return String Document type name.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public static function GetDocumentTypeName(String $DocumentType)
    {
        switch ($DocumentType) {
            case "01":
                return "Factura";
            case "03":
                return "Liquidación de Compras";
            case "04":
                return "Nota de Crédito ";
            case "05":
                return "Nota Débito";
            case "06":
                return "Guía de Remisión";
            case "07":
                return "Retención";
            default:
                return "";
        }
    }

    /**
     * Get document information based on the SRI key.
     *
     * @param String $SRI_key SRI Access/Authorization Key.
     * @return SimpleXMLElement Parsed XML document. 
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function getDocument(String $SRI_key)
    {
        $query = $this->queryToSRI($SRI_key);
        $docXML = simplexml_load_string($query, null);
        return $docXML;
    }

    /**
     * Process the XML document and extract relevant data.
     *
     * @param SimpleXMLElement $xml Parsed XML document.
     * @param Collection $data Initial data array.
     * @return array|null Updated data array or null if not authorized.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function getDocumentValue(\SimpleXMLElement $xmlComprobante, Collection $data)
    {
        $email = "";

        foreach ($xmlComprobante->children() as $section) {
            if ($section->getName() == self::getDocumentSection($data->get('TipoComprobante'))) {
                $this->extractReceptorInfo($section, $data);
            }

            if ($section->getName() == 'infoAdicional') {
                $email = $this->extractEmail($section);
            }
        }

        $data->put("EmailReceptor", $email);
        $data->put("RazonSocial", (string) $xmlComprobante->infoTributaria->razonSocial);
        $data->put("NombreComercial", (string) $xmlComprobante->infoTributaria->nombreComercial);
        $data->put("DireccionMatriz", (string) $xmlComprobante->infoTributaria->dirMatriz);

        return $data;
    }

    private function extractReceptorInfo($section, Collection $data)
    {
        foreach ($section->children() as $secc) {
            if (str_contains($secc->getName(), 'razonSocial')) {
                $data->put("Receptor", (string) $secc);
            }
            if (str_contains($secc->getName(), 'identificacion')) {
                $data->put("DNIReceptor", (string) $secc);
            }
            if (str_contains($secc->getName(), 'direccion')) {
                $data->put("DireccionReceptor", (string) $secc);
            }
        }
    }

    private function extractEmail(\SimpleXMLElement $section)
    {
        foreach ($section->children() as $secc) {
            if (str_contains($secc->attributes(), 'mail') || str_contains($secc->attributes(), 'correo')) {
                return (string) $secc;
            }
        }
        return "";
    }


    private function getDocumentAuthorization(\SimpleXMLElement $xmlAuthorization, Collection $data)
    {
        $data->put("Estado",  (string)$xmlAuthorization->estado);
        if ($xmlAuthorization->estado != "AUTORIZADO") {
            return false;
        }
        $data->put("FechaAutorizacion",  (string)$xmlAuthorization->fechaAutorizacion);
        $data->put("Ambiente",  (string)$xmlAuthorization->ambiente);
        return true;
    }

    /**
     * Get the XML section for the specific document type.
     *
     * @param String $DocumentType SRI Document Type Code.
     * @return String XML section name.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public static function getDocumentSection(String $DocumentSection)
    {
        switch ($DocumentSection) {
            case "01":
                return "infoFactura";
            case "03":
                return "infoLiquidacionCompra";
            case "04":
                return "infoNotaCredito";
            case "05":
                return "infoNotaDebito";
            case "06":
                return "infoGuiaRemision";
            case "07":
                return "infoCompRetencion";
            default:
                return "";
        }
    }




    /**
     * Query SRI for document authorization.
     *
     * @param String $SRI_key SRI Access/Authorization Key.
     * @return String Response XML.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private function queryToSRI(String $SRI_key)
    {
        $sriurl = config("app.sri_url");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $sriurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ec="http://ec.gob.sri.ws.autorizacion">
        <soapenv:Header/>
        <soapenv:Body>
        <ec:autorizacionComprobante>
         <!--Optional:-->
         <claveAccesoComprobante>' . $SRI_key . '</claveAccesoComprobante>
        </ec:autorizacionComprobante>
        </soapenv:Body>\\
        </soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml;charset=UTF-8'
            ),
        ));

        if (!$response = curl_exec($curl)) {
            trigger_error(curl_error($curl));
        }

        curl_close($curl);


        $xml = str_replace('<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:autorizacionComprobanteResponse xmlns:ns2="http://ec.gob.sri.ws.autorizacion">', "", $response);
        $xml = str_replace('</ns2:autorizacionComprobanteResponse></soap:Body></soap:Envelope>', "", $xml);

        return $xml;
    }
}
