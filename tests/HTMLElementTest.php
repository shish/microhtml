<?php

declare(strict_types=1);

require "vendor/autoload.php";

use function MicroHTML\A;
use function MicroHTML\P;
use function MicroHTML\INPUT;
use function MicroHTML\BR;
use function MicroHTML\emptyHTML;
use function MicroHTML\DIV;
use function MicroHTML\rawHTML;
use function MicroHTML\joinHTML;

class HTMLElementTest extends \PHPUnit\Framework\TestCase
{
    /*
     * basic
     */
    public function testBasic(): void
    {
        $this->assertEquals(
            "<p></p>",
            P()
        );
    }

    /*
     * attributes
     */
    public function testAttr(): void
    {
        $this->assertEquals(
            "<a href='hello.html'></a>",
            A(["href" => "hello.html"])
        );
    }

    public function testMultiAttr(): void
    {
        $this->assertEquals(
            "<a href='hello.html' target='_blank'></a>",
            A(["href" => "hello.html", "target" => "_blank"])
        );
    }

    public function testDangerAttr(): void
    {
        $this->assertEquals(
            "<a href='esc&#039; ape=&#039;foo'></a>",
            A(["href" => "esc' ape='foo"])
        );
    }

    public function testBoolAttrTrue(): void
    {
        $this->assertEquals(
            "<input required />",
            INPUT(["required" => true])
        );
    }

    public function testBoolAttrFalse(): void
    {
        $this->assertEquals(
            "<input />",
            INPUT(["required" => false])
        );
    }

    public function testNullAttr(): void
    {
        $this->assertEquals(
            "<input />",
            INPUT(["value" => null])
        );
    }

    public function testIntAttr(): void
    {
        $this->assertEquals(
            "<input value='42' />",
            INPUT(["value" => 42])
        );
    }

    /*
     * child elements
     */
    public function testChild(): void
    {
        $this->assertEquals(
            "<p><a></a></p>",
            P(A())
        );
    }

    public function testMultiChild(): void
    {
        $this->assertEquals(
            "<p><a></a><div></div></p>",
            P(A(), DIV())
        );
    }

    /*
     * text
     */
    public function testText(): void
    {
        $this->assertEquals(
            "<p>hello</p>",
            P("hello")
        );
    }

    public function testMutliText(): void
    {
        $this->assertEquals(
            "<p>helloworld</p>",
            P("hello", "world")
        );
    }

    public function testDangerText(): void
    {
        $this->assertEquals(
            "<p>&lt;a href=&#039;nope.html&#039;&gt;yo&lt;/a&gt;</p>",
            P("<a href='nope.html'>yo</a>")
        );
    }

    public function testNullText(): void
    {
        $this->assertEquals(
            "<p></p>",
            P(null)
        );
    }

    public function testIntText(): void
    {
        $this->assertEquals(
            "<p>42</p>",
            P(42)
        );
    }

    /*
     * functions
     */
    public function testAppendChild(): void
    {
        $el = P();
        $el->appendChild("hello world");
        $this->assertEquals(
            "<p>hello world</p>",
            $el
        );
    }

    /*
     * subclasses
     */
    public function testSelfClosing(): void
    {
        $this->assertEquals(
            "<br />",
            BR()
        );
    }

    public function testEmpty(): void
    {
        $this->assertEquals(
            "<br /><br />",
            emptyHTML(BR(), BR())
        );
    }

    public function testRaw(): void
    {
        $this->assertEquals(
            "<p><bacon></p>",
            P(rawHTML("<bacon>"))
        );
    }

    public function testJoin(): void
    {
        $this->assertEquals(
            "<p>A</p>, <p>B</p>, C",
            joinHTML(", ", [P("A"), P("B"), "C"])
        );
        $this->assertEquals(
            "<p>A</p><br /><p>B</p><br />C",
            joinHTML(BR(), [P("A"), P("B"), "C"])
        );
    }
}
