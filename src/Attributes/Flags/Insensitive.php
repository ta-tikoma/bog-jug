<?php

declare(strict_types=1);

namespace BogJug\Attributes\Flags;

use Attribute;

/**
 * Regex flag `i`
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Insensitive
{
}
