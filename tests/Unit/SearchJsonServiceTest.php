<?php

namespace Nourayman\SearchJson\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Nourayman\SearchJson\SearchJsonService;

class SearchJsonServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SearchJsonService();
    }

    public function testBuildRegexPattern()
    {
        $text = "الهاتف";
        $patterns = $this->service->buildRegexPattern($text);

        // Check if it returns an array
        $this->assertIsArray($patterns);

        // Check if the pattern matches the expected regex for each character
        $expectedPattern = "[إأآا]ل[هة][إأآا]تف"; // Based on your similarChars array
        $this->assertEquals($expectedPattern, $patterns[0]);
    }

    public function testBuildRegexPatternWithMultipleWords()
    {
        $text = "الهاتف المحمول";
        $patterns = $this->service->buildRegexPattern($text);

        // Check if it returns an array of patterns for each word
        $this->assertIsArray($patterns);
        $this->assertCount(2, $patterns);

        // Validate each word pattern
        $expectedPatterns = [
            "[إأآا]ل[هة][إأآا]تف",
            "[إأآا]لمحم[ووؤ]ل"
        ];

        $this->assertEquals($expectedPatterns[0], $patterns[0]);
        $this->assertEquals($expectedPatterns[1], $patterns[1]);
    }
}
