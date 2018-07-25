<?php
/**
 * Reclamation reasons.
 */

namespace App\Contracts\Shop\Reclamations;


interface ReclamationStatusInterface
{
    const REGISTERED = 1;

    const TESTING = 2;

    const ACCEPTED = 3;

    const NOT_ACCEPTED = 4;

    const EXCHANGED = 5;

    const WRITE_OFF = 6;

    const RETURNED = 7;

    const LOSSES = 8;
}