<?php

declare(strict_types=1);

class CodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Check for typos - function name should match tag name
     */
    public function testSync()
    {
        $exceptions = [
            "VAR_" => "var"
        ];
        foreach (file("src/microhtml.php") as $line) {
            if (preg_match("/function ([A-Z][^(]*)/", $line, $matches)) {
                $fun = $matches[1];
                $tag = $exceptions[$fun] ?? strtolower($fun);
                $this->assertStringContainsString("\"$tag\"", $line);
            }
        }
    }
}
