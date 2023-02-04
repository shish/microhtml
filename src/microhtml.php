<?php

declare(strict_types=1);

namespace MicroHTML;

class HTMLElement
{
    protected string $tag;
    protected array $attrs;
    protected array $children;

    public function __construct(string $tag, array $args)
    {
        $this->tag = $tag;

        if (count($args) > 0 && is_array($args[0])) {
            $this->attrs = $args[0];
            $this->children = array_slice($args, 1);
        } else {
            $this->attrs = [];
            $this->children = $args;
        }
    }

    public function appendChild(...$args): void
    {
        foreach ($args as $arg) {
            $this->children[] = $arg;
        }
    }

    protected function renderAttrs(): string
    {
        $par = "";
        foreach ($this->attrs as $name => $val) {
            if ($val === true) {
                $par .= " $name";
            } elseif ($val === false || $val === null) {
                // do nothing, so that boolean attributes can be
                // set to false, which is often easier than deleting
                // the key from the attributes dictionary
            } else {
                if (is_numeric($val)) {
                    $val = (string)$val;
                }
                $val = htmlentities($val, ENT_QUOTES, "UTF-8");
                $par .= " $name='$val'";
            }
        }
        return $par;
    }

    protected function renderChildren(): string
    {
        $sub = "";
        foreach ($this->children as $child) {
            if ($child instanceof HTMLElement) {
                $sub .= $child;
            } else {
                if (is_null($child)) {
                    $child = "";
                }
                if (is_numeric($child) || is_bool($child)) {
                    $child = (string)$child;
                }
                $sub .= htmlentities($child, ENT_QUOTES, "UTF-8");
            }
        }
        return $sub;
    }

    public function __toString(): string
    {
        $tag = $this->tag;
        $par = $this->renderAttrs();
        $sub = $this->renderChildren();
        return "<$tag$par>$sub</$tag>";
    }
}

class SelfClosingHTMLElement extends HTMLElement
{
    public function __toString(): string
    {
        $tag = $this->tag;
        $par = $this->renderAttrs();
        return "<$tag$par />";
    }
}

class EmptyHTMLElement extends HTMLElement
{
    public function __construct(array $args)
    {
        parent::__construct("", $args);
    }

    public function __toString(): string
    {
        $sub = $this->renderChildren();
        return "$sub";
    }
}

class RawHTMLElement extends HTMLElement
{
    private $html;

    public function __construct(string $html)
    {
        parent::__construct("", []);
        $this->html = $html;
    }

    public function __toString(): string
    {
        return $this->html;
    }
}

function rawHTML(string $html): HTMLElement
{
    return new RawHTMLElement($html);
}
function emptyHTML(...$args): HTMLElement
{
    return new EmptyHTMLElement($args);
}

# https://developer.mozilla.org/en-US/docs/Web/HTML/Element
function HTML(...$args): HTMLElement
{
    return new HTMLElement("html", $args);
}

# Document metadata
function BASE(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("base", $args);
}
function HEAD(...$args): HTMLElement
{
    return new HTMLElement("head", $args);
}
function LINK(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("link", $args);
}
function META(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("meta", $args);
}
function STYLE(...$args): HTMLElement
{
    return new HTMLElement("style", $args);
}
function TITLE(...$args): HTMLElement
{
    return new HTMLElement("title", $args);
}

# Sectioning root
function BODY(...$args): HTMLElement
{
    return new HTMLElement("body", $args);
}

# Content sectioning
function ADDRESS(...$args): HTMLElement
{
    return new HTMLElement("address", $args);
}
function ARTICLE(...$args): HTMLElement
{
    return new HTMLElement("article", $args);
}
function ASIDE(...$args): HTMLElement
{
    return new HTMLElement("aside", $args);
}
function FOOTER(...$args): HTMLElement
{
    return new HTMLElement("footer", $args);
}
function HEADER(...$args): HTMLElement
{
    return new HTMLElement("header", $args);
}
function H1(...$args): HTMLElement
{
    return new HTMLElement("h1", $args);
}
function H2(...$args): HTMLElement
{
    return new HTMLElement("h2", $args);
}
function H3(...$args): HTMLElement
{
    return new HTMLElement("h3", $args);
}
function H4(...$args): HTMLElement
{
    return new HTMLElement("h4", $args);
}
function H5(...$args): HTMLElement
{
    return new HTMLElement("h5", $args);
}
function H6(...$args): HTMLElement
{
    return new HTMLElement("h6", $args);
}
function HGROUP(...$args): HTMLElement
{
    return new HTMLElement("hgroup", $args);
}
function MAIN(...$args): HTMLElement
{
    return new HTMLElement("main", $args);
}
function NAV(...$args): HTMLElement
{
    return new HTMLElement("nav", $args);
}
function SECTION(...$args): HTMLElement
{
    return new HTMLElement("section", $args);
}

# Text content
function BLOCKQUOTE(...$args): HTMLElement
{
    return new HTMLElement("blockquote", $args);
}
function DD(...$args): HTMLElement
{
    return new HTMLElement("dd", $args);
}
function DIR(...$args): HTMLElement
{
    return new HTMLElement("dir", $args);
}
function DIV(...$args): HTMLElement
{
    return new HTMLElement("div", $args);
}
function DL(...$args): HTMLElement
{
    return new HTMLElement("dl", $args);
}
function DT(...$args): HTMLElement
{
    return new HTMLElement("dt", $args);
}
function FIGCAPTION(...$args): HTMLElement
{
    return new HTMLElement("figcaption", $args);
}
function FIGURE(...$args): HTMLElement
{
    return new HTMLElement("figure", $args);
}
function HR(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("hr", $args);
}
function LI(...$args): HTMLElement
{
    return new HTMLElement("li", $args);
}
function OL(...$args): HTMLElement
{
    return new HTMLElement("ol", $args);
}
function P(...$args): HTMLElement
{
    return new HTMLElement("p", $args);
}
function PRE(...$args): HTMLElement
{
    return new HTMLElement("pre", $args);
}
function UL(...$args): HTMLElement
{
    return new HTMLElement("ul", $args);
}

# Inline text semantics
function A(...$args): HTMLElement
{
    return new HTMLElement("a", $args);
}
function ABBR(...$args): HTMLElement
{
    return new HTMLElement("abbr", $args);
}
function B(...$args): HTMLElement
{
    return new HTMLElement("b", $args);
}
function BDI(...$args): HTMLElement
{
    return new HTMLElement("bdi", $args);
}
function BDO(...$args): HTMLElement
{
    return new HTMLElement("bdo", $args);
}
function BR(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("br", $args);
}
function CITE(...$args): HTMLElement
{
    return new HTMLElement("cite", $args);
}
function CODE(...$args): HTMLElement
{
    return new HTMLElement("code", $args);
}
function DATA(...$args): HTMLElement
{
    return new HTMLElement("data", $args);
}
function DFN(...$args): HTMLElement
{
    return new HTMLElement("dfn", $args);
}
function EM(...$args): HTMLElement
{
    return new HTMLElement("em", $args);
}
function I(...$args): HTMLElement
{
    return new HTMLElement("i", $args);
}
function KBD(...$args): HTMLElement
{
    return new HTMLElement("kbd", $args);
}
function MARK(...$args): HTMLElement
{
    return new HTMLElement("mark", $args);
}
function Q(...$args): HTMLElement
{
    return new HTMLElement("q", $args);
}
function RB(...$args): HTMLElement
{
    return new HTMLElement("rb", $args);
}
function RP(...$args): HTMLElement
{
    return new HTMLElement("rp", $args);
}
function RT(...$args): HTMLElement
{
    return new HTMLElement("rt", $args);
}
function RTC(...$args): HTMLElement
{
    return new HTMLElement("rtc", $args);
}
function RUBY(...$args): HTMLElement
{
    return new HTMLElement("ruby", $args);
}
function S(...$args): HTMLElement
{
    return new HTMLElement("s", $args);
}
function SAMP(...$args): HTMLElement
{
    return new HTMLElement("samp", $args);
}
function SMALL(...$args): HTMLElement
{
    return new HTMLElement("small", $args);
}
function SPAN(...$args): HTMLElement
{
    return new HTMLElement("span", $args);
}
function STRONG(...$args): HTMLElement
{
    return new HTMLElement("strong", $args);
}
function SUB(...$args): HTMLElement
{
    return new HTMLElement("sub", $args);
}
function SUP(...$args): HTMLElement
{
    return new HTMLElement("sup", $args);
}
function TIME(...$args): HTMLElement
{
    return new HTMLElement("time", $args);
}
function TT(...$args): HTMLElement
{
    return new HTMLElement("tt", $args);
}
function U(...$args): HTMLElement
{
    return new HTMLElement("u", $args);
}
function VAR_(...$args): HTMLElement
{
    return new HTMLElement("var", $args);
}
function WBR(...$args): HTMLElement
{
    return new HTMLElement("wbr", $args);
}

# Image and multimedia
function AREA(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("area", $args);
}
function AUDIO(...$args): HTMLElement
{
    return new HTMLElement("audio", $args);
}
function IMG(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("img", $args);
}
function MAP(...$args): HTMLElement
{
    return new HTMLElement("map", $args);
}
function TRACK(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("track", $args);
}
function VIDEO(...$args): HTMLElement
{
    return new HTMLElement("video", $args);
}

# Embedded content
function APPLET(...$args): HTMLElement
{
    return new HTMLElement("applet", $args);
}
function EMBED(...$args): HTMLElement
{
    return new HTMLElement("embed", $args);
}
function IFRAME(...$args): HTMLElement
{
    return new HTMLElement("iframe", $args);
}
function NOEMBED(...$args): HTMLElement
{
    return new HTMLElement("noembed", $args);
}
function OBJECT(...$args): HTMLElement
{
    return new HTMLElement("object", $args);
}
function PARAM(...$args): HTMLElement
{
    return new HTMLElement("param", $args);
}
function PICTURE(...$args): HTMLElement
{
    return new HTMLElement("picture", $args);
}
function SOURCE(...$args): HTMLElement
{
    return new HTMLElement("source", $args);
}

# Scripting
function CANVAS(...$args): HTMLElement
{
    return new HTMLElement("canvas", $args);
}
function NOSCRIPT(...$args): HTMLElement
{
    return new HTMLElement("noscript", $args);
}
function SCRIPT(...$args): HTMLElement
{
    return new HTMLElement("script", $args);
}

# Demarcating edits
function DEL(...$args): HTMLElement
{
    return new HTMLElement("del", $args);
}
function INS(...$args): HTMLElement
{
    return new HTMLElement("ins", $args);
}

# Table content
function CAPTION(...$args): HTMLElement
{
    return new HTMLElement("caption", $args);
}
function COL(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("col", $args);
}
function COLGROUP(...$args): HTMLElement
{
    return new HTMLElement("colgroup", $args);
}
function TABLE(...$args): HTMLElement
{
    return new HTMLElement("table", $args);
}
function TBODY(...$args): HTMLElement
{
    return new HTMLElement("tbody", $args);
}
function TD(...$args): HTMLElement
{
    return new HTMLElement("td", $args);
}
function TFOOT(...$args): HTMLElement
{
    return new HTMLElement("tfoot", $args);
}
function TH(...$args): HTMLElement
{
    return new HTMLElement("th", $args);
}
function THEAD(...$args): HTMLElement
{
    return new HTMLElement("thead", $args);
}
function TR(...$args): HTMLElement
{
    return new HTMLElement("tr", $args);
}

# Forms
function BUTTON(...$args): HTMLElement
{
    return new HTMLElement("button", $args);
}
function DATALIST(...$args): HTMLElement
{
    return new HTMLElement("datalist", $args);
}
function FIELDSET(...$args): HTMLElement
{
    return new HTMLElement("fieldset", $args);
}
function FORM(...$args): HTMLElement
{
    return new HTMLElement("form", $args);
}
function INPUT(...$args): HTMLElement
{
    return new SelfClosingHTMLElement("input", $args);
}
function LABEL(...$args): HTMLElement
{
    return new HTMLElement("label", $args);
}
function LEGEND(...$args): HTMLElement
{
    return new HTMLElement("legend", $args);
}
function METER(...$args): HTMLElement
{
    return new HTMLElement("meter", $args);
}
function OPTGROUP(...$args): HTMLElement
{
    return new HTMLElement("optgroup", $args);
}
function OPTION(...$args): HTMLElement
{
    return new HTMLElement("option", $args);
}
function OUTPUT(...$args): HTMLElement
{
    return new HTMLElement("output", $args);
}
function PROGRESS(...$args): HTMLElement
{
    return new HTMLElement("progress", $args);
}
function SELECT(...$args): HTMLElement
{
    return new HTMLElement("select", $args);
}
function TEXTAREA(...$args): HTMLElement
{
    return new HTMLElement("textarea", $args);
}

# Interactive elements
function DETAILS(...$args): HTMLElement
{
    return new HTMLElement("details", $args);
}
function DIALOG(...$args): HTMLElement
{
    return new HTMLElement("dialog", $args);
}
function SUMMARY(...$args): HTMLElement
{
    return new HTMLElement("summary", $args);
}
