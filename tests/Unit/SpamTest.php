<?php

namespace Tests\Feature;

use App\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_validates_spam()
    {
        // Given we have a spam detector
        $spam = new Spam();
        // When we analyze a string
        $bodyInnocent = 'Some innocent text';
        $bodyWithSpam = 'Some Shitty Forum';

        // Then it check if the string contains a spam and throws an exception
        $this->assertFalse($spam->detect($bodyInnocent));

        $this->expectException(\Exception::class);
        $spam->detect($bodyWithSpam);        
    }
}