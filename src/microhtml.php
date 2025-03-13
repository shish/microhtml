<?php

declare(strict_types=1);

namespace MicroHTML;

/**
 * @phpstan-type Attrs array<string,string|\Stringable|null|bool|int|float>
 * @phpstan-type Child \MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float
 * @phpstan-type Arg Attrs|Child
 */
class HTMLElement
{
    protected string $tag;
    /** @var Attrs $attrs */
    protected array $attrs;
    /** @var array<Child> $children */
    protected array $children;

    /** @param array<Arg> $args */
    public function __construct(string $tag, array $args)
    {
        $this->tag = $tag;

        if (count($args) > 0 && is_array($args[0])) {
            $this->attrs = $args[0];
            // @phpstan-ignore-next-line
            $this->children = array_slice($args, 1);
        } else {
            $this->attrs = [];
            // @phpstan-ignore-next-line
            $this->children = $args;
        }
    }

    /** @param Child $args */
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
                $val = htmlentities((string)$val, ENT_QUOTES, "UTF-8");
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
                if (is_null($child) || is_bool($child)) {
                    $child = "";
                }
                $sub .= htmlentities((string)$child, ENT_QUOTES, "UTF-8");
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

/**
 * https://developer.mozilla.org/en-US/docs/Glossary/Void_element
 *
 * @phpstan-import-type Attrs from HTMLElement
 */
class SelfClosingHTMLElement extends HTMLElement
{
    /**
     * @param Attrs $attrs
     */
    public function __construct(string $tag, array $attrs)
    {
        parent::__construct($tag, [$attrs]);
    }

    public function __toString(): string
    {
        $tag = $this->tag;
        $par = $this->renderAttrs();
        return "<$tag$par />";
    }
}

/**
 * @phpstan-import-type Child from HTMLElement
 */
class EmptyHTMLElement extends HTMLElement
{
    /**
     * @param array<Child> $args
     */
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

function emptyHTML(...$args): HTMLElement
{
    return new EmptyHTMLElement($args);
}

class RawHTMLElement extends HTMLElement
{
    private string $html;

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

/**
 * @param array<\MicroHTML\HTMLElement|string|null|bool|int|float> $pieces
 */
function joinHTML(HTMLElement|string $glue, array $pieces, bool $filterNulls = false): HTMLElement
{
    $out = emptyHTML();
    $n = 0;
    foreach ($pieces as $piece) {
        if ($filterNulls && $piece === null) {
            continue;
        }
        if ($n++ > 0) {
            $out->appendChild($glue);
        }
        $out->appendChild($piece);
    }
    return $out;
}

# https://developer.mozilla.org/en-US/docs/Web/HTML/Element
function HTML(...$args): HTMLElement
{
    return new HTMLElement("html", $args);
}

# Document metadata
/** @param array<string,string|null|bool|int|float> $attrs */
function BASE(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("base", $attrs);
}
function HEAD(...$args): HTMLElement
{
    return new HTMLElement("head", $args);
}
/** @param array<string,string|null|bool|int|float> $attrs */
function LINK(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("link", $attrs);
}
/** @param array<string,string|null|bool|int|float> $attrs */
function META(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("meta", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function HR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("hr", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function BR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("br", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function WBR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("wbr", $attrs);
}

# Image and multimedia
/** @param array<string,string|null|bool|int|float> $attrs */
function AREA(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("area", $attrs);
}
function AUDIO(...$args): HTMLElement
{
    return new HTMLElement("audio", $args);
}
/** @param array<string,string|null|bool|int|float> $attrs */
function IMG(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("img", $attrs);
}
function MAP(...$args): HTMLElement
{
    return new HTMLElement("map", $args);
}
/** @param array<string,string|null|bool|int|float> $attrs */
function TRACK(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("track", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function EMBED(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("embed", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function PARAM(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("param", $attrs);
}
function PICTURE(...$args): HTMLElement
{
    return new HTMLElement("picture", $args);
}
/** @param array<string,string|null|bool|int|float> $attrs */
function SOURCE(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("source", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function COL(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("col", $attrs);
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
/** @param array<string,string|null|bool|int|float> $attrs */
function INPUT(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("input", $attrs);
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
