<?php

require "vendor/autoload.php";

use function MicroHTML\{A,P,INPUT,BR,emptyHTML,DIV,rawHTML};

class HTMLElementTestCase extends \PHPUnit\Framework\TestCase {
	/*
	 * basic
	 */
	public function testBasic() {
		$this->assertEquals(
			"<p></p>",
			P()
		);
	}

	/*
	 * attributes
	 */
	public function testAttr() {
		$this->assertEquals(
			"<a href='hello.html'></a>",
			A(["href"=>"hello.html"])
		);
	}

	public function testMultiAttr() {
		$this->assertEquals(
			"<a href='hello.html' target='_blank'></a>",
			A(["href"=>"hello.html", "target"=>"_blank"])
		);
	}

	public function testDangerAttr() {
		$this->assertEquals(
			"<a href='esc&#039; ape=&#039;foo'></a>",
			A(["href"=>"esc' ape='foo"])
		);
	}

	public function testBoolAttr() {
		$this->assertEquals(
			"<input required />",
			INPUT(["required"=>true])
		);
	}

	/*
	 * child elements
	 */
	public function testChild() {
		$this->assertEquals(
			"<p><a></a></p>",
			P(A())
		);
	}

	public function testMultiChild() {
		$this->assertEquals(
			"<p><a></a><div></div></p>",
			P(A(), DIV())
		);
	}

	/*
	 * text
	 */
	public function testText() {
		$this->assertEquals(
			"<p>hello</p>",
			P("hello")
		);
	}

	public function testMutliText() {
		$this->assertEquals(
			"<p>helloworld</p>",
			P("hello", "world")
		);
	}

	public function testDangerText() {
		$this->assertEquals(
			"<p>&lt;a href=&#039;nope.html&#039;&gt;yo&lt;/a&gt;</p>",
			P("<a href='nope.html'>yo</a>")
		);
	}

	/*
	 * functions
	 */
	public function testAppendChild() {
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
	public function testSelfClosing() {
		$this->assertEquals(
			"<br />",
			BR()
		);
	}

	public function testEmpty() {
		$this->assertEquals(
			"<br /><br />",
			emptyHTML(BR(), BR())
		);
	}

	public function testRaw() {
		$this->assertEquals(
			"<p><bacon></p>",
			P(rawHTML("<bacon>"))
		);
	}
}
