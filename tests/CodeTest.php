<?php

declare(strict_types=1);

class CodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Check for typos - function name should match tag name
     */
    public function testSync(): void
    {
        $exceptions = [
            "VAR_" => "var"
        ];
        $lines = file("src/microhtml.php");
        if(!$lines) {
            $this->assertTrue($lines);
        }
        foreach ($lines as $line) {
            if (preg_match("/function ([A-Z][^(]*)/", $line, $matches)) {
                $fun = $matches[1];
                $tag = $exceptions[$fun] ?? strtolower($fun);
                // Call eg \MicroHTML\SECTION() and check that it contains "section"
                $this->assertStringContainsString($tag, (string)"\MicroHTML\\$fun"());
            }
        }
    }
}
