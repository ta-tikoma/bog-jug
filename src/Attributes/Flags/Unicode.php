<?php

declare(strict_types=1);

namespace BogJug\Attributes\Flags;

use Attribute;

/**
 * Regex flag `u`
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Unicode
{
}
