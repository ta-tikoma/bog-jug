<?php

declare(strict_types=1);

namespace BogJug\Services;

use Exception;
use ReflectionClass;
use ReflectionProperty;

final class AttributeService
{
    /**
     * find attribute by className, return attribute instance if found
     */
    public function getAttributeInstanseOrNull(
        ReflectionProperty|ReflectionClass $reflection,
        string $className
    ): mixed {
        $attributes = $reflection->getAttributes($className);
        if (count($attributes) === 0) {
            return null;
        }

        if (count($attributes) > 1) {
            throw new Exception("{$reflection->getName()} must has only one {$className} attribute");
        }

        return $attributes[0]->newInstance();
    }
}
