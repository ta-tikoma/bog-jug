<?php

declare(strict_types=1);

namespace BogJug\Services;

use BogJug\Attributes\Flags\_Global;
use BogJug\Attributes\Flags\Anchored;
use BogJug\Attributes\Flags\DollarEndOnly;
use BogJug\Attributes\Flags\Extended;
use BogJug\Attributes\Flags\Insensitive;
use BogJug\Attributes\Flags\Jchanged;
use BogJug\Attributes\Flags\MultiLine;
use BogJug\Attributes\Flags\SingleLine;
use BogJug\Attributes\Flags\Ungreedy;
use BogJug\Attributes\Flags\Unicode;
use Exception;
use ReflectionClass;

final class ClassService
{
    private const FLAGS = [
        'g' => _Global::class,
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

    public function getRegex(ReflectionClass $reflectionClass): string
    {
        $regex = '/';

        $properites = $reflectionClass->getProperties();
        if (count($properites) === 0) {
            throw new Exception("{$reflectionClass->getName()} has not properties");
        }

        foreach ($properites as $property) {
            $regex .= $this->propertyService->regexGroupFromProperty($property);
        }

        $regex .= '/' . $this->getFlags($reflectionClass);

        return $regex;
    }

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
