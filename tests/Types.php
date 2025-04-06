<?php

// This file gets tested by `composer stan` - phpstan should allow
// the allowed lines and reject the disallowed lines
declare(strict_types=1);

use function MicroHTML\{P, IMG};

// attributes should be array<string,string>
P(["class" => "foo"]); // properties allowed
P(["foo", "bar", "baz"]); // @phpstan-ignore-line - no non-property arrays allowed
P(P(), P()); // multiple children allowed
P([P(), P()]); // @phpstan-ignore-line - no arrays-of-children allowed

// void tags should have no children
IMG(["src" => "foo.jpg"]); // properties allowed
IMG(["foo", "bar"]); // @phpstan-ignore-line - no non-property arrays allowed
IMG(P()); // @phpstan-ignore-line - no children allowed
