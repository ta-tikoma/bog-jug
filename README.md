# Bog Jug
Helper for easy work with regex groups.  
Mapped regex group to php-classes, because array is sucks.

![Static Badge](https://img.shields.io/badge/PHP-8.1-brightgreen)
![Static Badge](https://img.shields.io/badge/PHPStan-level_8-brightgreen)
![Static Badge](https://img.shields.io/badge/PHPCS-PSR12-brightgreen)
![Static Badge](https://img.shields.io/badge/license-MIT-brightgreen)

# [Install](https://packagist.org/packages/ta-tikoma/bog-jug)
`composer require ta-tikoma/bog-jug`

# Use
- Create a class for regex descriptions.  
- Use property attributes for defined regex group: `#[Group('...')]` for group body; `#[After]` and `#[Before]` for defined outside symbols.  
- If you need to indicate the count of a group, use attributes like `#[ZeroOrOne]`, `#[ZeroOrMore]` and others from the namespace `BogJug\Attributes\Count`.  
- Finally, you can add regex flags via the class attributes; for example: `#[SingleLine]`.  
- Now create instance of class BogJug and use one of two base methods:  
    - Method `->one($regex, $text)` to find the first value equal to regex; analogue: `preg_match`.  
    - Method `->many($regex, $text)` to get all values equal regex; analog: `preg_match_all`.

# Sample example
#### 1. Define class of descriptions.
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
#### 2. Call method of BogJug.
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
#### 3. Take result.
```bash
^ tests\Data\TinWoodman^ {#427
  +head: "head"
  +arms: "arms"
  +legs: "legs"
  +body: "body"
  +heart: null
}
```
