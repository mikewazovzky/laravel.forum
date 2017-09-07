<?php

namespace Tests\Feature;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_validates_invalid_keywords()
    {
        // Given we have a spam detector
        $spam = new Spam();
        // When we analyze a string
        $bodyInnocent = 'Some innocent text';
        $bodyWithSpam = 'Some Shitty Forum';
        // Then it checks if the string contains a spam and throws an exception
        $this->assertFalse($spam->detect($bodyInnocent));
        $this->expectException(\Exception::class);
        $spam->detect($bodyWithSpam);        
    }

    /** @test */
    public function it_validates_key_held_down()
    {
        // Given we have a spam detector
        $spam = new Spam();
        // When we analyze a string
        $badText = 'Hello Woooooooooooooooo';
        // Then it checks if the string contains a keyhelddowns and throws an exception
        $this->assertFalse($spam->detect('Some innocernt text'));
        $this->expectException(\Exception::class);
        $spam->detect($badText);        
    }
}