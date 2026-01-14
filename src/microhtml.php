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
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function HTML($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("html", [$arg0, ...$args]);
}

# Document metadata
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function BASE(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("base", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function HEAD($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("head", [$arg0, ...$args]);
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
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function STYLE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("style", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TITLE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("title", [$arg0, ...$args]);
}

# Sectioning root
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function BODY($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("body", [$arg0, ...$args]);
}

# Content sectioning
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function ADDRESS($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("address", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function ARTICLE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("article", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function ASIDE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("aside", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function FOOTER($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("footer", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function HEADER($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("header", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function H1($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("h1", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function H2($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("h2", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function H3($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("h3", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function H4($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("h4", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function H5($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("h5", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function H6($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("h6", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function HGROUP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("hgroup", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function MAIN($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("main", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function NAV($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("nav", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SECTION($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("section", [$arg0, ...$args]);
}

# Text content
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function BLOCKQUOTE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("blockquote", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DD($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("dd", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DIR($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("dir", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DIV($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("div", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DL($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("dl", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("dt", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function FIGCAPTION($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("figcaption", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function FIGURE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("figure", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function HR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("hr", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function LI($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("li", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function OL($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("ol", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function P($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("p", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function PRE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("pre", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function UL($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("ul", [$arg0, ...$args]);
}

# Inline text semantics
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function A($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("a", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function ABBR($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("abbr", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function B($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("b", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function BDI($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("bdi", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function BDO($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("bdo", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function BR(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("br", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function CITE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("cite", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function CODE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("code", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DATA($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("data", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DFN($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("dfn", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function EM($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("em", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function I($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("i", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function KBD($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("kbd", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function MARK($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("mark", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function Q($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("q", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function RB($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("rb", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function RP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("rp", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function RT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("rt", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function RTC($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("rtc", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function RUBY($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("ruby", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function S($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("s", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SAMP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("samp", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SMALL($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("small", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SPAN($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("span", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function STRONG($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("strong", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SUB($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("sub", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SUP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("sup", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TIME($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("time", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("tt", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function U($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("u", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function VAR_($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("var", [$arg0, ...$args]);
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
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function AUDIO($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("audio", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function IMG(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("img", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function MAP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("map", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function TRACK(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("track", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function VIDEO($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("video", [$arg0, ...$args]);
}

# Embedded content
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function APPLET($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("applet", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function EMBED(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("embed", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function IFRAME($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("iframe", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function NOEMBED($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("noembed", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function OBJECT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("object", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function PARAM(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("param", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function PICTURE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("picture", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function SOURCE(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("source", $attrs);
}

# Scripting
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function CANVAS($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("canvas", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function NOSCRIPT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("noscript", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SCRIPT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("script", [$arg0, ...$args]);
}

# Demarcating edits
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DEL($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("del", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function INS($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("ins", [$arg0, ...$args]);
}

# Table content
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function CAPTION($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("caption", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function COL(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("col", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function COLGROUP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("colgroup", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TABLE($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("table", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TBODY($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("tbody", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TD($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("td", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TFOOT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("tfoot", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TH($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("th", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function THEAD($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("thead", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TR($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("tr", [$arg0, ...$args]);
}

# Forms
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function BUTTON($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("button", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DATALIST($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("datalist", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function FIELDSET($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("fieldset", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function FORM($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("form", [$arg0, ...$args]);
}
/** @param array<string,string|\Stringable|null|bool|int|float> $attrs */
function INPUT(array $attrs = []): SelfClosingHTMLElement
{
    return new SelfClosingHTMLElement("input", $attrs);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function LABEL($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("label", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function LEGEND($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("legend", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function METER($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("meter", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function OPTGROUP($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("optgroup", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function OPTION($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("option", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function OUTPUT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("output", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function PROGRESS($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("progress", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SELECT($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("select", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function TEXTAREA($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("textarea", [$arg0, ...$args]);
}

# Interactive elements
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DETAILS($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("details", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function DIALOG($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("dialog", [$arg0, ...$args]);
}
/**
 * @param array<string,string|\Stringable|null|bool|int|float>|\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float $arg0 - optional attribute array
 * @param array<\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float> $args
 */
function SUMMARY($arg0 = [], ...$args): HTMLElement
{
    return new HTMLElement("summary", [$arg0, ...$args]);
}
