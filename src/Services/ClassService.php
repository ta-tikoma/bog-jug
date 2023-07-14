<?php

declare(strict_types=1);

namespace BogJug\Services;

use BogJug\Attributes\Flags\Anchored;
use BogJug\Attributes\Flags\DollarEndOnly;
use BogJug\Attributes\Flags\Extended;
use BogJug\Attributes\Flags\Insensitive;
use BogJug\Attributes\Flags\Jchanged;
use BogJug\Attributes\Flags\MultiLine;
use BogJug\Attributes\Flags\SingleLine;
use BogJug\Attributes\Flags\Ungreedy;
use BogJug\Attributes\Flags\Unicode;
use ReflectionClass;

final class ClassService
{
    private const FLAGS = [
        'a' => Anchored::class,
        'd' => DollarEndOnly::class,
        'e' => Extended::class,
        'i' => Insensitive::class,
        'j' => Jchanged::class,
        'm' => MultiLine::class,
        's' => SingleLine::class,
        'U' => Ungreedy::class,
        'u' => Unicode::class,
    ];

    public function __construct(
        private AttributeService $attributeService,
        private PropertyService $propertyService
    ) {
    }

    public function getRegexWithFlags(ReflectionClass $reflectionClass): string
    {
        $regex = '/'
            . $this->propertyService->regexGroupFromProperties($reflectionClass)
            . '/'
            . $this->getFlags($reflectionClass)
            //
        ;

        return $regex;
    }

    /**
     * collect flags by attributes
     */
    private function getFlags(ReflectionClass $reflectionClass): string
    {
        $flags = [];

        foreach (self::FLAGS as $flag => $className) {
            /** @var mixed|null $inst */
            $inst = $this->attributeService->getAttributeInstanseOrNull($reflectionClass, $className);
            if ($inst !== null) {
                $flags[] = $flag;
            }
        }

        return implode('', $flags);
    }
}
