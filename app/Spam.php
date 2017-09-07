<?php

namespace App;

class Spam {
    /**
     * Create a new Spam instance.
     */
    public function __construct()
    {
        
    }

    public function detect($body)
    {
        // Detect invalid keywords.
        $this->detectInvalidKeywords($body);

        return false;
    }

    public function detectInvalidKeywords($body)
    {
        $invalidKeywords = [
            'yahoo customer support',
            'shit',
        ];

        foreach ($invalidKeywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new \Exception('Spam detected in your reply');
            }
        }
    }
}