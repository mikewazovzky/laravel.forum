<?php

namespace App\Inspections;

use Exception;

class InvalidKeywords {

    protected $invalidKeywords = [
        'yahoo customer support',
        'shit',
    ];

    public function detect($body)
    {      
       foreach ($this->invalidKeywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new Exception('Spam detected in your reply');
            }
        }
    }
}