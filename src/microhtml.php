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

/** @param \MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args */
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
/** @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args */
function HTML(...$args): HTMLElement
{
    return new HTMLElement("html", $args);
}

# Document metadata
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function BASE(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("base", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function HEAD(...$args): HTMLElement
{
    return new HTMLElement("head", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function LINK(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("link", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function META(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("meta", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function STYLE(...$args): HTMLElement
{
    return new HTMLElement("style", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TITLE(...$args): HTMLElement
{
    return new HTMLElement("title", $args);
}

# Sectioning root
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function BODY(...$args): HTMLElement
{
    return new HTMLElement("body", $args);
}

# Content sectioning
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function ADDRESS(...$args): HTMLElement
{
    return new HTMLElement("address", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function ARTICLE(...$args): HTMLElement
{
    return new HTMLElement("article", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function ASIDE(...$args): HTMLElement
{
    return new HTMLElement("aside", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function FOOTER(...$args): HTMLElement
{
    return new HTMLElement("footer", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function HEADER(...$args): HTMLElement
{
    return new HTMLElement("header", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function H1(...$args): HTMLElement
{
    return new HTMLElement("h1", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function H2(...$args): HTMLElement
{
    return new HTMLElement("h2", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function H3(...$args): HTMLElement
{
    return new HTMLElement("h3", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function H4(...$args): HTMLElement
{
    return new HTMLElement("h4", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function H5(...$args): HTMLElement
{
    return new HTMLElement("h5", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function H6(...$args): HTMLElement
{
    return new HTMLElement("h6", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function HGROUP(...$args): HTMLElement
{
    return new HTMLElement("hgroup", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function MAIN(...$args): HTMLElement
{
    return new HTMLElement("main", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function NAV(...$args): HTMLElement
{
    return new HTMLElement("nav", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SECTION(...$args): HTMLElement
{
    return new HTMLElement("section", $args);
}

# Text content
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function BLOCKQUOTE(...$args): HTMLElement
{
    return new HTMLElement("blockquote", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DD(...$args): HTMLElement
{
    return new HTMLElement("dd", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DIR(...$args): HTMLElement
{
    return new HTMLElement("dir", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DIV(...$args): HTMLElement
{
    return new HTMLElement("div", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DL(...$args): HTMLElement
{
    return new HTMLElement("dl", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DT(...$args): HTMLElement
{
    return new HTMLElement("dt", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function FIGCAPTION(...$args): HTMLElement
{
    return new HTMLElement("figcaption", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function FIGURE(...$args): HTMLElement
{
    return new HTMLElement("figure", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function HR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("hr", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function LI(...$args): HTMLElement
{
    return new HTMLElement("li", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function OL(...$args): HTMLElement
{
    return new HTMLElement("ol", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function P(...$args): HTMLElement
{
    return new HTMLElement("p", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function PRE(...$args): HTMLElement
{
    return new HTMLElement("pre", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function UL(...$args): HTMLElement
{
    return new HTMLElement("ul", $args);
}

# Inline text semantics
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function A(...$args): HTMLElement
{
    return new HTMLElement("a", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function ABBR(...$args): HTMLElement
{
    return new HTMLElement("abbr", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function B(...$args): HTMLElement
{
    return new HTMLElement("b", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function BDI(...$args): HTMLElement
{
    return new HTMLElement("bdi", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function BDO(...$args): HTMLElement
{
    return new HTMLElement("bdo", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function BR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("br", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function CITE(...$args): HTMLElement
{
    return new HTMLElement("cite", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function CODE(...$args): HTMLElement
{
    return new HTMLElement("code", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DATA(...$args): HTMLElement
{
    return new HTMLElement("data", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DFN(...$args): HTMLElement
{
    return new HTMLElement("dfn", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function EM(...$args): HTMLElement
{
    return new HTMLElement("em", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function I(...$args): HTMLElement
{
    return new HTMLElement("i", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function KBD(...$args): HTMLElement
{
    return new HTMLElement("kbd", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function MARK(...$args): HTMLElement
{
    return new HTMLElement("mark", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function Q(...$args): HTMLElement
{
    return new HTMLElement("q", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function RB(...$args): HTMLElement
{
    return new HTMLElement("rb", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function RP(...$args): HTMLElement
{
    return new HTMLElement("rp", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function RT(...$args): HTMLElement
{
    return new HTMLElement("rt", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function RTC(...$args): HTMLElement
{
    return new HTMLElement("rtc", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function RUBY(...$args): HTMLElement
{
    return new HTMLElement("ruby", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function S(...$args): HTMLElement
{
    return new HTMLElement("s", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SAMP(...$args): HTMLElement
{
    return new HTMLElement("samp", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SMALL(...$args): HTMLElement
{
    return new HTMLElement("small", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SPAN(...$args): HTMLElement
{
    return new HTMLElement("span", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function STRONG(...$args): HTMLElement
{
    return new HTMLElement("strong", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SUB(...$args): HTMLElement
{
    return new HTMLElement("sub", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SUP(...$args): HTMLElement
{
    return new HTMLElement("sup", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TIME(...$args): HTMLElement
{
    return new HTMLElement("time", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TT(...$args): HTMLElement
{
    return new HTMLElement("tt", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function U(...$args): HTMLElement
{
    return new HTMLElement("u", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function VAR_(...$args): HTMLElement
{
    return new HTMLElement("var", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function WBR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("wbr", $attrs);
}

# Image and multimedia
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function AREA(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("area", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function AUDIO(...$args): HTMLElement
{
    return new HTMLElement("audio", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function IMG(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("img", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function MAP(...$args): HTMLElement
{
    return new HTMLElement("map", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function TRACK(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("track", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function VIDEO(...$args): HTMLElement
{
    return new HTMLElement("video", $args);
}

# Embedded content
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function APPLET(...$args): HTMLElement
{
    return new HTMLElement("applet", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function EMBED(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("embed", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function IFRAME(...$args): HTMLElement
{
    return new HTMLElement("iframe", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function NOEMBED(...$args): HTMLElement
{
    return new HTMLElement("noembed", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function OBJECT(...$args): HTMLElement
{
    return new HTMLElement("object", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function PARAM(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("param", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function PICTURE(...$args): HTMLElement
{
    return new HTMLElement("picture", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function SOURCE(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("source", $attrs);
}

# Scripting
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function CANVAS(...$args): HTMLElement
{
    return new HTMLElement("canvas", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function NOSCRIPT(...$args): HTMLElement
{
    return new HTMLElement("noscript", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SCRIPT(...$args): HTMLElement
{
    return new HTMLElement("script", $args);
}

# Demarcating edits
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DEL(...$args): HTMLElement
{
    return new HTMLElement("del", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function INS(...$args): HTMLElement
{
    return new HTMLElement("ins", $args);
}

# Table content
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function CAPTION(...$args): HTMLElement
{
    return new HTMLElement("caption", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function COL(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("col", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function COLGROUP(...$args): HTMLElement
{
    return new HTMLElement("colgroup", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TABLE(...$args): HTMLElement
{
    return new HTMLElement("table", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TBODY(...$args): HTMLElement
{
    return new HTMLElement("tbody", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TD(...$args): HTMLElement
{
    return new HTMLElement("td", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TFOOT(...$args): HTMLElement
{
    return new HTMLElement("tfoot", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TH(...$args): HTMLElement
{
    return new HTMLElement("th", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function THEAD(...$args): HTMLElement
{
    return new HTMLElement("thead", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TR(...$args): HTMLElement
{
    return new HTMLElement("tr", $args);
}

# Forms
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function BUTTON(...$args): HTMLElement
{
    return new HTMLElement("button", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DATALIST(...$args): HTMLElement
{
    return new HTMLElement("datalist", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function FIELDSET(...$args): HTMLElement
{
    return new HTMLElement("fieldset", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function FORM(...$args): HTMLElement
{
    return new HTMLElement("form", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function INPUT(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("input", $attrs);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function LABEL(...$args): HTMLElement
{
    return new HTMLElement("label", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function LEGEND(...$args): HTMLElement
{
    return new HTMLElement("legend", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function METER(...$args): HTMLElement
{
    return new HTMLElement("meter", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function OPTGROUP(...$args): HTMLElement
{
    return new HTMLElement("optgroup", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function OPTION(...$args): HTMLElement
{
    return new HTMLElement("option", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function OUTPUT(...$args): HTMLElement
{
    return new HTMLElement("output", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function PROGRESS(...$args): HTMLElement
{
    return new HTMLElement("progress", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SELECT(...$args): HTMLElement
{
    return new HTMLElement("select", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function TEXTAREA(...$args): HTMLElement
{
    return new HTMLElement("textarea", $args);
}

# Interactive elements
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DETAILS(...$args): HTMLElement
{
    return new HTMLElement("details", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function DIALOG(...$args): HTMLElement
{
    return new HTMLElement("dialog", $args);
}
/** @param array<string,string|\Stringable|null|bool|int|float|array>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $args - attribute array or children */
function SUMMARY(...$args): HTMLElement
{
    return new HTMLElement("summary", $args);
}
