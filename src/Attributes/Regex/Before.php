<?php

declare(strict_types=1);

namespace BogJug\Attributes\Regex;

use Attribute;

/**
 * For define regex before group
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Before
{
    public function __construct(
        public readonly string $regex
    ) {
    }
}
