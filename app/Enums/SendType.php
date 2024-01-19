<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * Class SendType
 * This class defines an enumeration of send types with corresponding numeric values.
 * Each constant represents a specific send type, and the #[Description] attribute provides a
 * human-readable description for better understanding.
 * 
 * @method static static RestorePassword()
 * @method static static SendDocument() 
 * 
 * @package App\Enums
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
final class SendType extends Enum
{
    #[Description('Restaurar Contraseña')]
    const RestorePassword = 0;

    #[Description('Enviar Documento')]
    const SendDocument = 1;
}
