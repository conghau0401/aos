<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @param static CENTER_PICKUP
 * @param static DELIVERY
 * @param static SUPPLIER
 */
final class ShippingMethodEnum extends Enum
{
    const CENTER_PICKUP = "1";
    const DELIVERY = "2";
    const SUPPLIER = "3";
}
