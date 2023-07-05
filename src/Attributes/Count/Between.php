<?php

declare(strict_types=1);

namespace BogJug\Attributes\Count;

use Attribute;

/**
 * Group count, like `{3,6}`
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Between
{
    public function __construct(
        public readonly int $min,
        public readonly int $max,
    ) {
    }
}
