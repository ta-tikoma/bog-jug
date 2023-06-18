<?php

declare(strict_types=1);

namespace BogJug\Attributes\Regex;

use Attribute;

/**
 * For set group regex base
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Group
{
    public function __construct(
        public readonly string $regex
    ) {
    }
}
