<?php

declare(strict_types=1);

namespace BogJug\Services;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

final class TypeService
{
    public function isArray(ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type): bool
    {
        if ($type === null) {
            return true;
        }

        if ($type instanceof ReflectionNamedType) {
            if ($type->getName() === 'array') {
                return true;
            }

            return false;
        }

        /* $type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType */
        foreach ($type->getTypes() as $subType) {
            if ($subType->getName() === 'array') {
                return true;
            }
        }

        return false;
    }
}
