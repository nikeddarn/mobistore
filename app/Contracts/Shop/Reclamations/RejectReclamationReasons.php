<?php
/**
 * Reclamation reasons.
 */

namespace App\Contracts\Shop\Reclamations;


interface RejectReclamationReasons
{
    const UNIDENTIFIED_PRODUCT = 1;

    const WARRANTY_EXPIRED = 2;

    const SEAL_BROKEN = 3;

    const PROTECTIVE_FILM_REMOVED = 4;

    const MECHANICAL_DAMAGE = 5;
}