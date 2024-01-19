<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * Class EmissionType
 * This class defines an enumeration of emission types with corresponding numeric values.
 * Each constant represents a specific emission type, and the #[Description] attribute provides a
 * human-readable description for better understanding.
 * 
 * @method static static Pruebas()
 * @method static static Produccion()
 * 
 * @package App\Enums
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
final class EmissionType extends Enum
{
    #[Description('Emisión Pruebas')]
    const Pruebas = 0;

    #[Description('Emisión Producción')]
    const Produccion = 1;
}
