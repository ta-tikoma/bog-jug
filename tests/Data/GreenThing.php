<?php

declare(strict_types=1);

namespace tests\Data;

use BogJug\Attributes\Regex\Group;

final class GreenThing
{
    public function __construct(
        #[Group('green\w* \w+')]
        public readonly string $thing,
    ) {
    }
}
