<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * Class SMTPSecurityType
 * This class defines an enumeration of SMTP security types with corresponding numeric values.
 * Each constant represents a specific SMTP security type, and the #[Description] attribute provides a
 * human-readable description for better understanding.
 * 
 * @method static static SSL()
 * @method static static TLS() 
 * 
 * @package App\Enums
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
final class SMTPSecurityType extends Enum
{
    #[Description('SSL Security')]
    const SSL = 0;

    #[Description('TLS Security')]
    const TLS = 1;
}
