<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;

class CodeTest extends \PHPUnit\Framework\TestCase
{
    public const EXCEPTIONS = [
        "VAR_" => "var"
    ];
    public const VOID_TAGS = [
        "area",
        "base",
        "br",
        "col",
        "embed",
        "hr",
        "img",
        "input",
        "link",
        "meta",
        "param",
        "source",
        "track",
        "wbr"
    ];

    /**
     * Get a list of all tag-generation functions in the MicroHTML library.
     *
     * @return array<array<string>> An array of tag names.
     */
    public static function getFunctionList(): array
    {
        $lines = file("src/microhtml.php");
        if (!$lines) {
            throw new \RuntimeException("Failed to read src/microhtml.php");
        }
        $tags = [];
        foreach ($lines as $line) {
            if (preg_match("/function ([A-Z][^(]*)/", $line, $matches)) {
                $tags[] = [$matches[1]];
            }
        }
        return $tags;
    }

    public function testDataProvider(): void
    {
        $this->assertNotEmpty($this->getFunctionList());
    }

    /**
     * Check for typos - function name should match tag name
     *
     * @param string $function The function name to validate.
     */
    #[DataProvider('getFunctionList')]
    public function testTag(string $function): void
    {
        $tag = self::EXCEPTIONS[$function] ?? strtolower($function);
        $name = "\MicroHTML\\$function";
        $this->assertIsCallable($name);
        $element = $name();
        // Call eg \MicroHTML\SECTION() and check that it contains "section"
        $this->assertStringContainsString($tag, (string)$element);
        // Check that void tags inherit from SelfClosingHTMLElement
        if (in_array($tag, self::VOID_TAGS)) {
            $this->assertInstanceOf(\MicroHTML\SelfClosingHTMLElement::class, $element);
        } else {
            $this->assertNotInstanceOf(\MicroHTML\SelfClosingHTMLElement::class, $element);
        }
    }
}
