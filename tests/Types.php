<?php

// This file gets tested by `composer stan` - phpstan should allow
// the allowed lines and reject the disallowed lines
declare(strict_types=1);

use function MicroHTML\{P, IMG};

P(["class" => "foo"]); // attributes allowed in first argument
P("words", ["property" => "array"]); // @phpstan-ignore-line - attributes not allowed in any other argument
P(["foo", "bar", "baz"]); // @phpstan-ignore-line - attribute arrays must be string=>string
P(P(), P()); // multiple children allowed
P([P(), P()]); // @phpstan-ignore-line - no arrays-of-children allowed

// void tags should have no children
IMG(["src" => "foo.jpg"]); // attributes allowed
IMG(["foo", "bar"]); // @phpstan-ignore-line - attribute arrays must be string=>string
IMG(P()); // @phpstan-ignore-line - children not allowed
