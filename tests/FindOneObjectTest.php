<?php

declare(strict_types=1);

namespace tests;

use BogJug\BogJug;
use PHPUnit\Framework\TestCase;
use tests\Data\TinWoodman;

final class FindOneObjectTest extends TestCase
{
    public function test_try_to_parse_one_match_to_object(): void
    {
        $bj = (new BogJug);
        $tw = $bj->one(TinWoodman::class, <<<OZ
One of the big trees had been partly chopped through, and standing beside
it, with an uplifted axe in his hands, was a man made entirely of tin. His head
and arms and legs were jointed upon his body, but he stood perfectly motionless, 
as if he could not stir at all.
OZ);

        $this->assertNotNull($tw);
        $this->assertNotNull($tw->head);
        $this->assertNotNull($tw->arms);
        $this->assertNotNull($tw->legs);
        $this->assertNotNull($tw->body);
        $this->assertNull($tw->heart);
    }
}
