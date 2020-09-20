<?php declare(strict_types=1);

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
			elseif($val === false || $val === null) {
				// do nothing, so that boolean attributes can be
				// set to false, which is often easier than deleting
				// the key from the attributes dictionary
			}
			else {
				if(is_numeric($val)) $val = (string)$val;
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

function rawHTML(string $html) {return new RawHTMLElement($html);}
function emptyHTML(...$args) {return new EmptyHTMLElement($args);}

# https://developer.mozilla.org/en-US/docs/Web/HTML/Element
function HTML(...$args) {return new HTMLElement("html", $args);}

# Document metadata
function BASE(...$args) {return new SelfClosingHTMLElement("base", $args);}
function HEAD(...$args) {return new HTMLElement("head", $args);}
function LINK(...$args) {return new SelfClosingHTMLElement("link", $args);}
function META(...$args) {return new SelfClosingHTMLElement("meta", $args);}
function STYLE(...$args) {return new HTMLElement("style", $args);}
function TITLE(...$args) {return new HTMLElement("title", $args);}

# Sectioning root
function BODY(...$args) {return new HTMLElement("body", $args);}

# Content sectioning
function ADDRESS(...$args) {return new HTMLElement("address", $args);}
function ARTICLE(...$args) {return new HTMLElement("article", $args);}
function ASIDE(...$args) {return new HTMLElement("aside", $args);}
function FOOTER(...$args) {return new HTMLElement("footer", $args);}
function HEADER(...$args) {return new HTMLElement("header", $args);}
function H1(...$args) {return new HTMLElement("h1", $args);}
function H2(...$args) {return new HTMLElement("h2", $args);}
function H3(...$args) {return new HTMLElement("h3", $args);}
function H4(...$args) {return new HTMLElement("h4", $args);}
function H5(...$args) {return new HTMLElement("h5", $args);}
function H6(...$args) {return new HTMLElement("h6", $args);}
function HGROUP(...$args) {return new HTMLElement("hgroup", $args);}
function MAIN(...$args) {return new HTMLElement("main", $args);}
function NAV(...$args) {return new HTMLElement("nav", $args);}
function SECTION(...$args) {return new HTMLElement("section", $args);}

# Text content
function BLOCKQUOTE(...$args) {return new HTMLElement("blockquote", $args);}
function DD(...$args) {return new HTMLElement("dd", $args);}
function DIR(...$args) {return new HTMLElement("dir", $args);}
function DIV(...$args) {return new HTMLElement("div", $args);}
function DL(...$args) {return new HTMLElement("dl", $args);}
function DT(...$args) {return new HTMLElement("dt", $args);}
function FIGCAPTION(...$args) {return new HTMLElement("figcaption", $args);}
function FIGURE(...$args) {return new HTMLElement("figure", $args);}
function HR(...$args) {return new SelfClosingHTMLElement("hr", $args);}
function LI(...$args) {return new HTMLElement("li", $args);}
function OL(...$args) {return new HTMLElement("ol", $args);}
function P(...$args) {return new HTMLElement("p", $args);}
function PRE(...$args) {return new HTMLElement("pre", $args);}
function UL(...$args) {return new HTMLElement("ul", $args);}

# Inline text semantics
function A(...$args) {return new HTMLElement("a", $args);}
function ABBR(...$args) {return new HTMLElement("abbr", $args);}
function B(...$args) {return new HTMLElement("b", $args);}
function BDI(...$args) {return new HTMLElement("bdi", $args);}
function BDO(...$args) {return new HTMLElement("bdo", $args);}
function BR(...$args) {return new SelfClosingHTMLElement("br", $args);}
function CITE(...$args) {return new HTMLElement("cite", $args);}
function CODE(...$args) {return new HTMLElement("code", $args);}
function DATA(...$args) {return new HTMLElement("data", $args);}
function DFN(...$args) {return new HTMLElement("dfn", $args);}
function EM(...$args) {return new HTMLElement("em", $args);}
function I(...$args) {return new HTMLElement("i", $args);}
function KBD(...$args) {return new HTMLElement("kbd", $args);}
function MARK(...$args) {return new HTMLElement("mark", $args);}
function Q(...$args) {return new HTMLElement("q", $args);}
function RB(...$args) {return new HTMLElement("rb", $args);}
function RP(...$args) {return new HTMLElement("rp", $args);}
function RT(...$args) {return new HTMLElement("rt", $args);}
function RTC(...$args) {return new HTMLElement("rtc", $args);}
function RUBY(...$args) {return new HTMLElement("ruby", $args);}
function S(...$args) {return new HTMLElement("s", $args);}
function SAMP(...$args) {return new HTMLElement("samp", $args);}
function SMALL(...$args) {return new HTMLElement("small", $args);}
function SPAN(...$args) {return new HTMLElement("span", $args);}
function STRONG(...$args) {return new HTMLElement("strong", $args);}
function SUB(...$args) {return new HTMLElement("sub", $args);}
function SUP(...$args) {return new HTMLElement("sup", $args);}
function TIME(...$args) {return new HTMLElement("time", $args);}
function TT(...$args) {return new HTMLElement("tt", $args);}
function U(...$args) {return new HTMLElement("u", $args);}
function VAR_(...$args) {return new HTMLElement("var", $args);}
function WBR(...$args) {return new HTMLElement("wbr", $args);}

# Image and multimedia
function AREA(...$args) {return new SelfClosingHTMLElement("area", $args);}
function AUDIO(...$args) {return new HTMLElement("audio", $args);}
function IMG(...$args) {return new SelfClosingHTMLElement("img", $args);}
function MAP(...$args) {return new HTMLElement("map", $args);}
function TRACK(...$args) {return new SelfClosingHTMLElement("track", $args);}
function VIDEO(...$args) {return new HTMLElement("video", $args);}

# Embedded content
function APPLET(...$args) {return new HTMLElement("applet", $args);}
function EMBED(...$args) {return new HTMLElement("embed", $args);}
function IFRAME(...$args) {return new HTMLElement("iframe", $args);}
function NOEMBED(...$args) {return new HTMLElement("noembed", $args);}
function OBJECT(...$args) {return new HTMLElement("object", $args);}
function PARAM(...$args) {return new HTMLElement("param", $args);}
function PICTURE(...$args) {return new HTMLElement("picture", $args);}
function SOURCE(...$args) {return new HTMLElement("source", $args);}

# Scripting
function CANVAS(...$args) {return new HTMLElement("canvas", $args);}
function NOSCRIPT(...$args) {return new HTMLElement("noscript", $args);}
function SCRIPT(...$args) {return new HTMLElement("script", $args);}

# Demarcating edits
function DEL(...$args) {return new HTMLElement("del", $args);}
function INS(...$args) {return new HTMLElement("ins", $args);}

# Table content
function CAPTION(...$args) {return new HTMLElement("caption", $args);}
function COL(...$args) {return new SelfClosingHTMLElement("col", $args);}
function COLGROUP(...$args) {return new HTMLElement("colgroup", $args);}
function TABLE(...$args) {return new HTMLElement("table", $args);}
function TBODY(...$args) {return new HTMLElement("tbody", $args);}
function TD(...$args) {return new HTMLElement("td", $args);}
function TFOOT(...$args) {return new HTMLElement("tfoot", $args);}
function TH(...$args) {return new HTMLElement("th", $args);}
function THEAD(...$args) {return new HTMLElement("thead", $args);}
function TR(...$args) {return new HTMLElement("tr", $args);}

# Forms
function BUTTON(...$args) {return new HTMLElement("button", $args);}
function DATALIST(...$args) {return new HTMLElement("datalist", $args);}
function FIELDSET(...$args) {return new HTMLElement("fieldset", $args);}
function FORM(...$args) {return new HTMLElement("form", $args);}
function INPUT(...$args) {return new SelfClosingHTMLElement("input", $args);}
function LABEL(...$args) {return new HTMLElement("label", $args);}
function LEGEND(...$args) {return new HTMLElement("legend", $args);}
function METER(...$args) {return new HTMLElement("meter", $args);}
function OPTGROUP(...$args) {return new HTMLElement("optgroup", $args);}
function OPTION(...$args) {return new HTMLElement("option", $args);}
function OUTPUT(...$args) {return new HTMLElement("output", $args);}
function PROGRESS(...$args) {return new HTMLElement("progress", $args);}
function SELECT(...$args) {return new HTMLElement("select", $args);}
function TEXTAREA(...$args) {return new HTMLElement("textarea", $args);}

# Interactive elements
function DETAILS(...$args) {return new HTMLElement("details", $args);}
function DIALOG(...$args) {return new HTMLElement("dialog", $args);}
function SUMMARY(...$args) {return new HTMLElement("summary", $args);}
