<?php

declare(strict_types=1);

namespace BogJug\Services;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

final class TypeService
{
    /**
     * @todo
     */
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

    public function equalsType(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $actual,
        string $expected
    ): bool {
        if ($actual === null) {
            return false;
        }

        if ($actual instanceof ReflectionNamedType) {
            if ($actual->getName() === $expected) {
                return true;
            }

            return false;
        }

        /* $type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType */
        foreach ($actual->getTypes() as $subType) {
            if ($subType->getName() === $expected) {
                return true;
            }
        }

        return false;
    }

    public function getClassOfType(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type
    ): ReflectionClass|null {
        if ($type === null) {
            return null;
        }

        if ($type instanceof ReflectionNamedType) {
            $name = $type->getName();

            if (class_exists($name)) {
                return new ReflectionClass($name);
            }

            return null;
        }

        return null;
    }
}
