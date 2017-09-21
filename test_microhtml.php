<?php

namespace MicroHTML;

require_once "microhtml.php";

class HTMLElementTestCase extends \PHPUnit\Framework\TestCase {
	protected $backupGlobalsBlacklist = array('database', 'config');

	# $this->assertEqualXMLStructure();
	/*
	 * basic
	 */
	function testBasic() {
		$this->assertEquals(
			"<p></p>",
			P()
		);
	}

	/*
	 * attributes
	 */
	function testAttr() {
		$this->assertEquals(
			"<a href='hello.html'></a>",
			A(["href"=>"hello.html"])
		);
	}

	function testMultiAttr() {
		$this->assertEquals(
			"<a href='hello.html' target='_blank'></a>",
			A(["href"=>"hello.html", "target"=>"_blank"])
		);
	}

	function testDangerAttr() {
		$this->assertEquals(
			"<a href='esc&#039; ape=&#039;foo'></a>",
			A(["href"=>"esc' ape='foo"])
		);
	}

	function testBoolAttr() {
		$this->assertEquals(
			"<input required />",
			INPUT(["required"=>true])
		);
	}

	/*
	 * child elements
	 */
	function testChild() {
		$this->assertEquals(
			"<p><a></a></p>",
			P(A())
		);
	}

	function testMultiChild() {
		$this->assertEquals(
			"<p><a></a><div></div></p>",
			P(A(), DIV())
		);
	}

	/*
	 * text
	 */
	function testText() {
		$this->assertEquals(
			"<p>hello</p>",
			P("hello")
		);
	}

	function testMutliText() {
		$this->assertEquals(
			"<p>helloworld</p>",
			P("hello", "world")
		);
	}

	function testDangerText() {
		$this->assertEquals(
			"<p>&lt;a href=&#039;nope.html&#039;&gt;yo&lt;/a&gt;</p>",
			P("<a href='nope.html'>yo</a>")
		);
	}

	/*
	 * functions
	 */
	function testAppendChild() {
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
	function testSelfClosing() {
		$this->assertEquals(
			"<br />",
			BR()
		);
	}

	function testEmpty() {
		$this->assertEquals(
			"<br /><br />",
			emptyHTML(BR(), BR())
		);
	}

	function testRaw() {
		$this->assertEquals(
			"<p><bacon></p>",
			P(rawHTML("<bacon>"))
		);
	}
}
