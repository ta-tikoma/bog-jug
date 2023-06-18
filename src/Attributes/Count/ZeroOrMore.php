<?php

declare(strict_types=1);

namespace BogJug\Attributes\Count;

use Attribute;

/**
 * Group count, like `*`
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class ZeroOrMore
{
}
