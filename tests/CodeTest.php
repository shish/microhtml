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
        $this->assertNotFalse($lines);
        foreach ($lines as $line) {
            if (preg_match("/function ([A-Z][^(]*)/", $line, $matches)) {
                $fun = $matches[1];
                $tag = $exceptions[$fun] ?? strtolower($fun);
                $name = "\MicroHTML\\$fun";
                $this->assertIsCallable($name);
                // Call eg \MicroHTML\SECTION() and check that it contains "section"
                $this->assertStringContainsString($tag, (string)$name());
            }
        }
    }
}
