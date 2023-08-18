<?php

declare(strict_types=1);

namespace tests\Data;

use BogJug\Attributes\Flags\SingleLine;
use BogJug\Attributes\Regex\After;
use BogJug\Attributes\Regex\Group;

#[SingleLine]
final class TinWoodman
{
    public function __construct(
        #[Group('head')]
        #[After('.*')]
        public readonly string $noggin,
        #[Group('arms')]
        #[After('.*')]
        public readonly string $upperLimbs,
        #[Group('legs')]
        #[After('.*')]
        public readonly string $lowerLimbs,
        #[Group('body')]
        #[After('.*')]
        public readonly string $torso,
        #[Group('heart')]
        #[After('.*')]
        public readonly string|null $coeur,
    ) {
    }
}
