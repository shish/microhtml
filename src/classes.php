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
