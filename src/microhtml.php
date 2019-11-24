<?php

namespace MicroHTML;

class HTMLElement {
	protected $tag = null;
	protected $attrs = array();
	protected $children = array();

	public function __construct(string $tag, array $args) {
		$this->tag = $tag;

		if(count($args) > 0 && is_array($args[0])) {
			$this->attrs = $args[0];
			$this->children = array_slice($args, 1);
		}
		else {
			$this->attrs = [];
			$this->children = $args;
		}
	}

	public function appendChild(...$args) {
		foreach($args as $arg) {
			$this->children[] = $arg;
		}
	}

	protected function renderAttrs(): string {
		$par = "";
		foreach($this->attrs as $name => $val) {
			if($val === true) {
				$par .= " $name";
			}
			else {
				$val = htmlentities($val, ENT_QUOTES, "UTF-8");
				$par .= " $name='$val'";
			}
		}
		return $par;
	}

	protected function renderChildren(): string {
		$sub = "";
		foreach($this->children as $child) {
			if($child instanceof HTMLElement) {
				$sub .= $child;
			}
			else {
				$sub .= htmlentities($child, ENT_QUOTES, "UTF-8");
			}
		}
		return $sub;
	}

	public function __toString() {
		$tag = $this->tag;
		$par = $this->renderAttrs();
		$sub = $this->renderChildren();
		return "<$tag$par>$sub</$tag>";
	}
}

class SelfClosingHTMLElement extends HTMLElement {
	public function __toString() {
		$tag = $this->tag;
		$par = $this->renderAttrs();
		return "<$tag$par />";
	}
}

class EmptyHTMLElement extends HTMLElement {
	public function __construct(array $args) {
		parent::__construct("", $args);
	}

	public function __toString() {
		$sub = $this->renderChildren();
		return "$sub";
	}
}

class RawHTMLElement extends HTMLElement {
	private $html;

	public function __construct(string $html) {
		parent::__construct("", []);
		$this->html = $html;
	}

	public function __toString() {
		return $this->html;
	}
}

function HTML(...$args) {return new HTMLElement("html", $args);}
function HEAD(...$args) {return new HTMLElement("head", $args);}
function BODY(...$args) {return new HTMLElement("body", $args);}

function TABLE(...$args) {return new HTMLElement("table", $args);}
function THEAD(...$args) {return new HTMLElement("thead", $args);}
function TBODY(...$args) {return new HTMLElement("tbody", $args);}
function TFOOT(...$args) {return new HTMLElement("tfoot", $args);}
function TH(...$args) {return new HTMLElement("th", $args);}
function TR(...$args) {return new HTMLElement("tr", $args);}
function TD(...$args) {return new HTMLElement("td", $args);}

function A(...$args) {return new HTMLElement("a", $args);}
function P(...$args) {return new HTMLElement("p", $args);}
function DIV(...$args) {return new HTMLElement("div", $args);}
function SPAN(...$args) {return new HTMLElement("span", $args);}
function SMALL(...$args) {return new HTMLElement("small", $args);}
function B(...$args) {return new HTMLElement("b", $args);}
function I(...$args) {return new HTMLElement("i", $args);}
function U(...$args) {return new HTMLElement("u", $args);}
function SCRIPT(...$args) {return new HTMLElement("script", $args);}
function SECTION(...$args) {return new HTMLElement("section", $args);}
function H1(...$args) {return new HTMLElement("h1", $args);}
function H2(...$args) {return new HTMLElement("h2", $args);}
function H3(...$args) {return new HTMLElement("h3", $args);}

function FORM(...$args) {return new HTMLElement("form", $args);}
function LABEL(...$args) {return new HTMLElement("label", $args);}
function SELECT(...$args) {return new HTMLElement("select", $args);}
function OPTION(...$args) {return new HTMLElement("option", $args);}

function INPUT(...$args) {return new SelfClosingHTMLElement("input", $args);}
function BR(...$args) {return new SelfClosingHTMLElement("br", $args);}
function IMG(...$args) {return new SelfClosingHTMLElement("img", $args);}

function rawHTML(string $html) {return new RawHTMLElement($html);}
function emptyHTML(...$args) {return new EmptyHTMLElement($args);}
