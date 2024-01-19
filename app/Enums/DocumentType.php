<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * Class DocumentType
 * This class defines an enumeration of document types with corresponding numeric values.
 * Each constant represents a specific document type, and the #[Description] attribute provides a
 * human-readable description for better understanding.
 * 
 * @method static static Factura()
 * @method static static NotaCredito()
 * @method static static NotaDebito()
 * @method static static Retencion()
 * @method static static GuiaRemision()
 * @method static static LiquidacionCompras()
 * 
 * @package App\Enums
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
final class DocumentType extends Enum
{
    #[Description('Factura')]
    const Factura = 1;

    #[Description('Nota de Crédito')]
    const NotaCredito = 2;
    
    #[Description('Nota Débito')]
    const NotaDebito = 3;
    
    #[Description('Retención')]
    const Retencion = 4;
    
    #[Description('Guía de Remisión')]
    const GuiaRemision = 5;
    
    #[Description('Liquidación de Compras')]
    const LiquidacionCompras = 6;
}
