<?php

declare(strict_types=1);

namespace MicroHTML;

class HTMLElement
{
    protected string $tag;
    /** @var array<string, mixed> */
    protected array $attrs;
    /** @var mixed[] */
    protected array $children;

    /**
     * @param mixed[] $args
     */
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

    public function appendChild(mixed ...$args): void
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
function emptyHTML(mixed ...$args): HTMLElement
{
    return new EmptyHTMLElement($args);
}
/** @param mixed[] $pieces */
function joinHTML(HTMLElement|string $glue, array $pieces): HTMLElement
{
    $out = emptyHTML();
    $n = 0;
    foreach ($pieces as $piece) {
        if ($n++ > 0) {
            $out->appendChild($glue);
        }
        $out->appendChild($piece);
    }
    return $out;
}

# https://developer.mozilla.org/en-US/docs/Web/HTML/Element
function HTML(mixed ...$args): HTMLElement
{
    return new HTMLElement("html", $args);
}

# Document metadata
function BASE(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("base", $args);
}
function HEAD(mixed ...$args): HTMLElement
{
    return new HTMLElement("head", $args);
}
function LINK(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("link", $args);
}
function META(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("meta", $args);
}
function STYLE(mixed ...$args): HTMLElement
{
    return new HTMLElement("style", $args);
}
function TITLE(mixed ...$args): HTMLElement
{
    return new HTMLElement("title", $args);
}

# Sectioning root
function BODY(mixed ...$args): HTMLElement
{
    return new HTMLElement("body", $args);
}

# Content sectioning
function ADDRESS(mixed ...$args): HTMLElement
{
    return new HTMLElement("address", $args);
}
function ARTICLE(mixed ...$args): HTMLElement
{
    return new HTMLElement("article", $args);
}
function ASIDE(mixed ...$args): HTMLElement
{
    return new HTMLElement("aside", $args);
}
function FOOTER(mixed ...$args): HTMLElement
{
    return new HTMLElement("footer", $args);
}
function HEADER(mixed ...$args): HTMLElement
{
    return new HTMLElement("header", $args);
}
function H1(mixed ...$args): HTMLElement
{
    return new HTMLElement("h1", $args);
}
function H2(mixed ...$args): HTMLElement
{
    return new HTMLElement("h2", $args);
}
function H3(mixed ...$args): HTMLElement
{
    return new HTMLElement("h3", $args);
}
function H4(mixed ...$args): HTMLElement
{
    return new HTMLElement("h4", $args);
}
function H5(mixed ...$args): HTMLElement
{
    return new HTMLElement("h5", $args);
}
function H6(mixed ...$args): HTMLElement
{
    return new HTMLElement("h6", $args);
}
function HGROUP(mixed ...$args): HTMLElement
{
    return new HTMLElement("hgroup", $args);
}
function MAIN(mixed ...$args): HTMLElement
{
    return new HTMLElement("main", $args);
}
function NAV(mixed ...$args): HTMLElement
{
    return new HTMLElement("nav", $args);
}
function SECTION(mixed ...$args): HTMLElement
{
    return new HTMLElement("section", $args);
}

# Text content
function BLOCKQUOTE(mixed ...$args): HTMLElement
{
    return new HTMLElement("blockquote", $args);
}
function DD(mixed ...$args): HTMLElement
{
    return new HTMLElement("dd", $args);
}
function DIR(mixed ...$args): HTMLElement
{
    return new HTMLElement("dir", $args);
}
function DIV(mixed ...$args): HTMLElement
{
    return new HTMLElement("div", $args);
}
function DL(mixed ...$args): HTMLElement
{
    return new HTMLElement("dl", $args);
}
function DT(mixed ...$args): HTMLElement
{
    return new HTMLElement("dt", $args);
}
function FIGCAPTION(mixed ...$args): HTMLElement
{
    return new HTMLElement("figcaption", $args);
}
function FIGURE(mixed ...$args): HTMLElement
{
    return new HTMLElement("figure", $args);
}
function HR(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("hr", $args);
}
function LI(mixed ...$args): HTMLElement
{
    return new HTMLElement("li", $args);
}
function OL(mixed ...$args): HTMLElement
{
    return new HTMLElement("ol", $args);
}
function P(mixed ...$args): HTMLElement
{
    return new HTMLElement("p", $args);
}
function PRE(mixed ...$args): HTMLElement
{
    return new HTMLElement("pre", $args);
}
function UL(mixed ...$args): HTMLElement
{
    return new HTMLElement("ul", $args);
}

# Inline text semantics
function A(mixed ...$args): HTMLElement
{
    return new HTMLElement("a", $args);
}
function ABBR(mixed ...$args): HTMLElement
{
    return new HTMLElement("abbr", $args);
}
function B(mixed ...$args): HTMLElement
{
    return new HTMLElement("b", $args);
}
function BDI(mixed ...$args): HTMLElement
{
    return new HTMLElement("bdi", $args);
}
function BDO(mixed ...$args): HTMLElement
{
    return new HTMLElement("bdo", $args);
}
function BR(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("br", $args);
}
function CITE(mixed ...$args): HTMLElement
{
    return new HTMLElement("cite", $args);
}
function CODE(mixed ...$args): HTMLElement
{
    return new HTMLElement("code", $args);
}
function DATA(mixed ...$args): HTMLElement
{
    return new HTMLElement("data", $args);
}
function DFN(mixed ...$args): HTMLElement
{
    return new HTMLElement("dfn", $args);
}
function EM(mixed ...$args): HTMLElement
{
    return new HTMLElement("em", $args);
}
function I(mixed ...$args): HTMLElement
{
    return new HTMLElement("i", $args);
}
function KBD(mixed ...$args): HTMLElement
{
    return new HTMLElement("kbd", $args);
}
function MARK(mixed ...$args): HTMLElement
{
    return new HTMLElement("mark", $args);
}
function Q(mixed ...$args): HTMLElement
{
    return new HTMLElement("q", $args);
}
function RB(mixed ...$args): HTMLElement
{
    return new HTMLElement("rb", $args);
}
function RP(mixed ...$args): HTMLElement
{
    return new HTMLElement("rp", $args);
}
function RT(mixed ...$args): HTMLElement
{
    return new HTMLElement("rt", $args);
}
function RTC(mixed ...$args): HTMLElement
{
    return new HTMLElement("rtc", $args);
}
function RUBY(mixed ...$args): HTMLElement
{
    return new HTMLElement("ruby", $args);
}
function S(mixed ...$args): HTMLElement
{
    return new HTMLElement("s", $args);
}
function SAMP(mixed ...$args): HTMLElement
{
    return new HTMLElement("samp", $args);
}
function SMALL(mixed ...$args): HTMLElement
{
    return new HTMLElement("small", $args);
}
function SPAN(mixed ...$args): HTMLElement
{
    return new HTMLElement("span", $args);
}
function STRONG(mixed ...$args): HTMLElement
{
    return new HTMLElement("strong", $args);
}
function SUB(mixed ...$args): HTMLElement
{
    return new HTMLElement("sub", $args);
}
function SUP(mixed ...$args): HTMLElement
{
    return new HTMLElement("sup", $args);
}
function TIME(mixed ...$args): HTMLElement
{
    return new HTMLElement("time", $args);
}
function TT(mixed ...$args): HTMLElement
{
    return new HTMLElement("tt", $args);
}
function U(mixed ...$args): HTMLElement
{
    return new HTMLElement("u", $args);
}
function VAR_(mixed ...$args): HTMLElement
{
    return new HTMLElement("var", $args);
}
function WBR(mixed ...$args): HTMLElement
{
    return new HTMLElement("wbr", $args);
}

# Image and multimedia
function AREA(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("area", $args);
}
function AUDIO(mixed ...$args): HTMLElement
{
    return new HTMLElement("audio", $args);
}
function IMG(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("img", $args);
}
function MAP(mixed ...$args): HTMLElement
{
    return new HTMLElement("map", $args);
}
function TRACK(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("track", $args);
}
function VIDEO(mixed ...$args): HTMLElement
{
    return new HTMLElement("video", $args);
}

# Embedded content
function APPLET(mixed ...$args): HTMLElement
{
    return new HTMLElement("applet", $args);
}
function EMBED(mixed ...$args): HTMLElement
{
    return new HTMLElement("embed", $args);
}
function IFRAME(mixed ...$args): HTMLElement
{
    return new HTMLElement("iframe", $args);
}
function NOEMBED(mixed ...$args): HTMLElement
{
    return new HTMLElement("noembed", $args);
}
function OBJECT(mixed ...$args): HTMLElement
{
    return new HTMLElement("object", $args);
}
function PARAM(mixed ...$args): HTMLElement
{
    return new HTMLElement("param", $args);
}
function PICTURE(mixed ...$args): HTMLElement
{
    return new HTMLElement("picture", $args);
}
function SOURCE(mixed ...$args): HTMLElement
{
    return new HTMLElement("source", $args);
}

# Scripting
function CANVAS(mixed ...$args): HTMLElement
{
    return new HTMLElement("canvas", $args);
}
function NOSCRIPT(mixed ...$args): HTMLElement
{
    return new HTMLElement("noscript", $args);
}
function SCRIPT(mixed ...$args): HTMLElement
{
    return new HTMLElement("script", $args);
}

# Demarcating edits
function DEL(mixed ...$args): HTMLElement
{
    return new HTMLElement("del", $args);
}
function INS(mixed ...$args): HTMLElement
{
    return new HTMLElement("ins", $args);
}

# Table content
function CAPTION(mixed ...$args): HTMLElement
{
    return new HTMLElement("caption", $args);
}
function COL(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("col", $args);
}
function COLGROUP(mixed ...$args): HTMLElement
{
    return new HTMLElement("colgroup", $args);
}
function TABLE(mixed ...$args): HTMLElement
{
    return new HTMLElement("table", $args);
}
function TBODY(mixed ...$args): HTMLElement
{
    return new HTMLElement("tbody", $args);
}
function TD(mixed ...$args): HTMLElement
{
    return new HTMLElement("td", $args);
}
function TFOOT(mixed ...$args): HTMLElement
{
    return new HTMLElement("tfoot", $args);
}
function TH(mixed ...$args): HTMLElement
{
    return new HTMLElement("th", $args);
}
function THEAD(mixed ...$args): HTMLElement
{
    return new HTMLElement("thead", $args);
}
function TR(mixed ...$args): HTMLElement
{
    return new HTMLElement("tr", $args);
}

# Forms
function BUTTON(mixed ...$args): HTMLElement
{
    return new HTMLElement("button", $args);
}
function DATALIST(mixed ...$args): HTMLElement
{
    return new HTMLElement("datalist", $args);
}
function FIELDSET(mixed ...$args): HTMLElement
{
    return new HTMLElement("fieldset", $args);
}
function FORM(mixed ...$args): HTMLElement
{
    return new HTMLElement("form", $args);
}
function INPUT(mixed ...$args): HTMLElement
{
    return new SelfClosingHTMLElement("input", $args);
}
function LABEL(mixed ...$args): HTMLElement
{
    return new HTMLElement("label", $args);
}
function LEGEND(mixed ...$args): HTMLElement
{
    return new HTMLElement("legend", $args);
}
function METER(mixed ...$args): HTMLElement
{
    return new HTMLElement("meter", $args);
}
function OPTGROUP(mixed ...$args): HTMLElement
{
    return new HTMLElement("optgroup", $args);
}
function OPTION(mixed ...$args): HTMLElement
{
    return new HTMLElement("option", $args);
}
function OUTPUT(mixed ...$args): HTMLElement
{
    return new HTMLElement("output", $args);
}
function PROGRESS(mixed ...$args): HTMLElement
{
    return new HTMLElement("progress", $args);
}
function SELECT(mixed ...$args): HTMLElement
{
    return new HTMLElement("select", $args);
}
function TEXTAREA(mixed ...$args): HTMLElement
{
    return new HTMLElement("textarea", $args);
}

# Interactive elements
function DETAILS(mixed ...$args): HTMLElement
{
    return new HTMLElement("details", $args);
}
function DIALOG(mixed ...$args): HTMLElement
{
    return new HTMLElement("dialog", $args);
}
function SUMMARY(mixed ...$args): HTMLElement
{
    return new HTMLElement("summary", $args);
}
