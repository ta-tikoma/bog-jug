<?php

declare(strict_types=1);

namespace tests;

use BogJug\BogJug;
use PHPUnit\Framework\TestCase;
use tests\Data\GreenThing;
use tests\Data\TinWoodman;

final class FindOneObjectTest extends TestCase
{
    public function test_try_to_parse_one_match_to_object(): void
    {
        $bj = new BogJug();

        /** @var TinWoodman|null $tw */
        $tw = $bj->one(TinWoodman::class, <<<OZ
One of the big trees had been partly chopped through, and standing beside
it, with an uplifted axe in his hands, was a man made entirely of tin. His head
and arms and legs were jointed upon his body, but he stood perfectly motionless, 
as if he could not stir at all.
OZ);

        $this->assertNotNull($tw);
        $this->assertNotNull($tw->noggin);
        $this->assertNotNull($tw->upperLimbs);
        $this->assertNotNull($tw->lowerLimbs);
        $this->assertNotNull($tw->torso);
        $this->assertNull($tw->coeur);
    }

    public function test_try_to_parse_objects(): void
    {
        $bj = new BogJug();

        $things = $bj->many(GreenThing::class, <<<OZ
There were many people — men, women, and children — walking about,
and these were all dressed in green clothes and had greenish skins. They
looked at Dorothy and her strangely assorted company with wondering eyes,
and the children all ran away and hid behind their mothers when they saw the
Lion; but no one spoke to them. Many shops stood in the street, and Dorothy
saw that everything in them was green. Green candy and green pop corn were
offered for sale, as well as green shoes, green hats, and green clothes of all sorts.
At one place a man was selling green lemonade, and when the children bought
it Dorothy could see that they paid for it with green pennies.
OZ);

        $this->assertCount(8, $things);
    }
}
