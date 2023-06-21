# Bog Jug
Convert regex group to objects

# Install

# Use

# Example
#### 1. Define result object
```php
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
        #[Group('head'), After('.*')]
        public readonly string $head,
        #[Group('arms'), After('.*')]
        public readonly string $arms,
        #[Group('legs'), After('.*')]
        public readonly string $legs,
        #[Group('body'), After('.*')]
        public readonly string $body,
        #[Group('heart'), After('.*')]
        public readonly string|null $heart,
    ) {
    }
}
```
#### 2. Call
```php
        $bj = (new BogJug);
        $tw = $bj->one(TinWoodman::class, <<<OZ
One of the big trees had been partly chopped through, and standing beside
it, with an uplifted axe in his hands, was a man made entirely of tin. His head
and arms and legs were jointed upon his body, but he stood perfectly motionless, 
as if he could not stir at all.
OZ);
        dump($tw);
```
#### 3. Result
```bash
^ tests\Data\TinWoodman^ {#427
  +head: "head"
  +arms: "arms"
  +legs: "legs"
  +body: "body"
  +heart: null
}
```
