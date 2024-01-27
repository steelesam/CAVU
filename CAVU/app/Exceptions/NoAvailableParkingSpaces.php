<?php

namespace App\Exceptions;

use Exception;

class NoAvailableParkingSpaces extends Exception
{
    /**
     * Create an instance of no parking available exception.
     *
     * @param  string  $message
     */
    public function __construct($message = 'No parking spaces currently available.', $code = 0)
    {
        parent::__construct($message, $code);
    }
}
