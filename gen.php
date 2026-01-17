<?php

/**
 * Generator script for microhtml.php
 *
 * This script generates the src/microhtml.php file by combining:
 * 1. Core classes from src/classes.php
 * 2. Generated HTML element functions based on the $elements definition
 */

enum ElementType
{
    case Normal;
    case Void;
}

// Define all HTML elements organized by category
$elements = [
    'https://developer.mozilla.org/en-US/docs/Web/HTML/Element' => [
        'Main root' => [
            'HTML' => ElementType::Normal,
        ],
        'Document metadata' => [
            'BASE' => ElementType::Void,
            'HEAD' => ElementType::Normal,
            'LINK' => ElementType::Void,
            'META' => ElementType::Void,
            'STYLE' => ElementType::Normal,
            'TITLE' => ElementType::Normal,
        ],
        'Sectioning root' => [
            'BODY' => ElementType::Normal,
        ],
        'Content sectioning' => [
            'ADDRESS' => ElementType::Normal,
            'ARTICLE' => ElementType::Normal,
            'ASIDE' => ElementType::Normal,
            'FOOTER' => ElementType::Normal,
            'HEADER' => ElementType::Normal,
            'H1' => ElementType::Normal,
            'H2' => ElementType::Normal,
            'H3' => ElementType::Normal,
            'H4' => ElementType::Normal,
            'H5' => ElementType::Normal,
            'H6' => ElementType::Normal,
            'HGROUP' => ElementType::Normal,
            'MAIN' => ElementType::Normal,
            'NAV' => ElementType::Normal,
            'SECTION' => ElementType::Normal,
        ],
        'Text content' => [
            'BLOCKQUOTE' => ElementType::Normal,
            'DD' => ElementType::Normal,
            'DIR' => ElementType::Normal,
            'DIV' => ElementType::Normal,
            'DL' => ElementType::Normal,
            'DT' => ElementType::Normal,
            'FIGCAPTION' => ElementType::Normal,
            'FIGURE' => ElementType::Normal,
            'HR' => ElementType::Void,
            'LI' => ElementType::Normal,
            'OL' => ElementType::Normal,
            'P' => ElementType::Normal,
            'PRE' => ElementType::Normal,
            'UL' => ElementType::Normal,
        ],
        'Inline text semantics' => [
            'A' => ElementType::Normal,
            'ABBR' => ElementType::Normal,
            'B' => ElementType::Normal,
            'BDI' => ElementType::Normal,
            'BDO' => ElementType::Normal,
            'BR' => ElementType::Void,
            'CITE' => ElementType::Normal,
            'CODE' => ElementType::Normal,
            'DATA' => ElementType::Normal,
            'DFN' => ElementType::Normal,
            'EM' => ElementType::Normal,
            'I' => ElementType::Normal,
            'KBD' => ElementType::Normal,
            'MARK' => ElementType::Normal,
            'Q' => ElementType::Normal,
            'RB' => ElementType::Normal,
            'RP' => ElementType::Normal,
            'RT' => ElementType::Normal,
            'RTC' => ElementType::Normal,
            'RUBY' => ElementType::Normal,
            'S' => ElementType::Normal,
            'SAMP' => ElementType::Normal,
            'SMALL' => ElementType::Normal,
            'SPAN' => ElementType::Normal,
            'STRONG' => ElementType::Normal,
            'SUB' => ElementType::Normal,
            'SUP' => ElementType::Normal,
            'TIME' => ElementType::Normal,
            'TT' => ElementType::Normal,
            'U' => ElementType::Normal,
            'VAR_' => ElementType::Normal,  // VAR_ because var is a reserved word
            'WBR' => ElementType::Void,
        ],
        'Image and multimedia' => [
            'AREA' => ElementType::Void,
            'AUDIO' => ElementType::Normal,
            'IMG' => ElementType::Void,
            'MAP' => ElementType::Normal,
            'TRACK' => ElementType::Void,
            'VIDEO' => ElementType::Normal,
        ],
        'Embedded content' => [
            'APPLET' => ElementType::Normal,
            'EMBED' => ElementType::Void,
            'IFRAME' => ElementType::Normal,
            'NOEMBED' => ElementType::Normal,
            'OBJECT' => ElementType::Normal,
            'PARAM' => ElementType::Void,
            'PICTURE' => ElementType::Normal,
            'SOURCE' => ElementType::Void,
        ],
        'Scripting' => [
            'CANVAS' => ElementType::Normal,
            'NOSCRIPT' => ElementType::Normal,
            'SCRIPT' => ElementType::Normal,
        ],
        'Demarcating edits' => [
            'DEL' => ElementType::Normal,
            'INS' => ElementType::Normal,
        ],
        'Table content' => [
            'CAPTION' => ElementType::Normal,
            'COL' => ElementType::Void,
            'COLGROUP' => ElementType::Normal,
            'TABLE' => ElementType::Normal,
            'TBODY' => ElementType::Normal,
            'TD' => ElementType::Normal,
            'TFOOT' => ElementType::Normal,
            'TH' => ElementType::Normal,
            'THEAD' => ElementType::Normal,
            'TR' => ElementType::Normal,
        ],
        'Forms' => [
            'BUTTON' => ElementType::Normal,
            'DATALIST' => ElementType::Normal,
            'FIELDSET' => ElementType::Normal,
            'FORM' => ElementType::Normal,
            'INPUT' => ElementType::Void,
            'LABEL' => ElementType::Normal,
            'LEGEND' => ElementType::Normal,
            'METER' => ElementType::Normal,
            'OPTGROUP' => ElementType::Normal,
            'OPTION' => ElementType::Normal,
            'OUTPUT' => ElementType::Normal,
            'PROGRESS' => ElementType::Normal,
            'SELECT' => ElementType::Normal,
            'TEXTAREA' => ElementType::Normal,
        ],
        'Interactive elements' => [
            'DETAILS' => ElementType::Normal,
            'DIALOG' => ElementType::Normal,
            'SUMMARY' => ElementType::Normal,
        ],
    ],
];
$attrsType = 'array<string,string|\Stringable|null|bool|int|float>';
$phpAttrsType = preg_replace('/<.*>/', '', $attrsType);
$childType = '\MicroHTML\HTMLElement|string|\Stringable|null|bool|int|float';

function generateNormalElement(string $funcName, string $tag): string
{
    global $attrsType, $phpAttrsType, $childType;
    return <<<EOD
/**
 * @param $attrsType|$childType \$arg0 HTML attributes or first child
 * @param $childType \$args - any further children
 */
function $funcName(
    $phpAttrsType|$childType \$arg0 = [],
    $childType ...\$args,
): HTMLElement {
    return new HTMLElement("$tag", [\$arg0, ...\$args]);
}
EOD;
}

function generateSelfClosingElement(string $funcName, string $tag): string
{
    global $attrsType, $phpAttrsType;
    return <<<EOD
/** @param $attrsType \$attrs */
function $funcName(
    $phpAttrsType \$attrs = [],
): SelfClosingHTMLElement {
    return new SelfClosingHTMLElement("$tag", \$attrs);
}
EOD;
}

function generateFile(array $elements): string
{
    $output = "<?php\n\n";
    $output .= "declare(strict_types=1);\n\n";
    $output .= "/**\n";
    $output .= " * This file is auto-generated. Do not edit directly.\n";
    $output .= " * Generated by gen.php\n";
    $output .= " */\n\n";
    $output .= "namespace MicroHTML;\n\n";
    $output .= "require_once __DIR__ . '/classes.php';\n\n";

    foreach ($elements as $url => $categories) {
        $output .= "# $url";

        foreach ($categories as $category => $elems) {
            $output .= "\n# $category\n";

            foreach ($elems as $funcName => $type) {
                $tag = rtrim($funcName, '_');
                $tag = strtolower($tag);

                if ($type === ElementType::Void) {
                    $output .= generateSelfClosingElement($funcName, $tag) . "\n";
                } else {
                    $output .= generateNormalElement($funcName, $tag) . "\n";
                }
            }
        }
    }

    return $output;
}

$content = generateFile($elements);
file_put_contents(__DIR__ . '/src/microhtml.php', $content);

echo "Generated src/microhtml.php successfully!\n";
