<?php

declare(strict_types=1);

namespace BogJug\Attributes\Count;

use Attribute;

/**
 * Group count, like `{3}`
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class Exactly
{
    public function __construct(
        public readonly int $count
    ) {
    }
}
