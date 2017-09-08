<?php

namespace App\Rules;

use App\Inspections\Spam;

class SpamFree {

    /**
     * Check if attribute value passes a validation
     *
     * @param mixed $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            return ! resolve(Spam::class)->detect($value);
        } catch (\Exception $e) {
            return false;
        }
    }
}