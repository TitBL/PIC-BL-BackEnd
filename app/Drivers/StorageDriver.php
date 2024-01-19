<?php

namespace App\Drivers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class StorageDriver
 * Static class for managing the repository.
 *
 * @package App\Drivers
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class StorageDriver
{
    const COMPANY_NAME = 'empresa';
    /**
     * Crea un directorio dentro de una unidad de Storage.
     *
     * @param string $directory Directorio a crear.
     * @param string|null $disk Nombre de la unidad a usar, si es omitido toma el valor 'local'.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    static function CreateDirectory($directory,  String $disk = null)
    {
        $disk = self::getDefaultLocalDisk($disk);

        if (isset($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }
    }

    /**
     * Get the path where electronic documents will be stored.
     *
     * @param string $RUC Value of the company's RUC.
     * @return string Company's path.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    static function GetCompanyFolder(string $RUC)
    {
        return  self::COMPANY_NAME . '/' . $RUC . '/';
    }

    /**
     * Create a directory and store a file in a Storage unit.
     *
     * @param object|null $file File object to save.
     * @param string|null $directory Directory to create.
     * @param string|null $name New name of the file to store, optional.
     * @param string|null $disk Name of the unit to use, if omitted, it defaults to 'local'.
     * @return array|null ["status" => bool, "filename" => string] or null.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    static function SaveToStorage($file = null, $directory = null, $name = null, String $disk = null)
    {
        $disk = self::getDefaultLocalDisk($disk);

        if (isset($file, $directory)) {
            $name = $name ?? $file->getClientOriginalName();
            $name = $name . '.' . $file->getClientOriginalExtension();
            $newDirectory = $directory . $name;
            self::createDirectory($directory, $disk);

            return [
                "status" => Storage::disk($disk)->put($directory . $name, File::get($file)),
                "filename" => $newDirectory
            ];
        }

        return null;
    }

    /**
     * Convert an image to base64code.
     *
     * @param string|null $disk Name of the unit to use, if omitted, it defaults to 'public'.
     * @param string|null $file Name of the file stored in the unit for which to perform the conversion.
     * @return string String with the image encoding in base64code.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    static function ImageToBase64(String $file = null, String $disk = null)
    {
        $disk = self::getDefaultPublicDisk($disk);
        $file = $file ?? '/logos/app.png';

        $pathfile =   Storage::disk($disk)->path($file);
        $type = pathinfo($pathfile, PATHINFO_EXTENSION);
        $data = file_get_contents($pathfile);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Get the content of a file.
     *
     * @param string|null $filePath The path to the file.
     *
     * @return string The content of the file.
     *
     * @throws  FileNotFoundException
     */
    public static function GetFile(string $filePath = null): string
    {
        $disk = self::getDefaultLocalDisk();

        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->get($filePath);
        } else {
            throw new FileNotFoundException("The file does not exist.");
        }
    }


    /**
     * Check if the file exists.
     *
     * @param string|null $disk Name of the unit to use, if omitted, it defaults to 'local'.
     * @param string|null $pathFile Path of the file within the unit.
     * @return bool Indicates whether the file exists.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    static function FileExists(String $PatchFile = null, String $disk = null)
    {
        if (!isset($PatchFile)) {
            return false;
        }
        $disk = self::getDefaultPublicDisk($disk);
        return Storage::disk($disk)->exists($PatchFile);
    }


    static function SaveXML(\SimpleXMLElement $XMl,   String $pathfile)
    {
        $disk = self::getDefaultLocalDisk();
        if (!self::FileExists($pathfile)) {
            Storage::disk($disk)->put($pathfile, $XMl->asXML());
        }
    }


    /**
     * Get the default disk value if not provided.
     *
     * @param string|null $disk Name of the unit to use, if omitted, it defaults to 'public'.
     * @return string
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private static function getDefaultPublicDisk(string $disk = null)
    {
        return $disk ?? 'public';
    }

    /**
     * Get the default disk value if not provided.
     *
     * @param string|null $disk Name of the unit to use, if omitted, it defaults to 'local'.
     * @return string
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private static function getDefaultLocalDisk(string $disk = null)
    {
        return $disk ?? 'local';
    }
}
