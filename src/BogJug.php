<?php

declare(strict_types=1);

namespace BogJug;

use BogJug\Services\AttributeService;
use BogJug\Services\ClassService;
use BogJug\Services\PropertyService;
use BogJug\Services\TypeService;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

final class BogJug
{
    private ClassService $classService;

    private TypeService $typeService;

    public function __construct()
    {
        $attributeService = new AttributeService();
        $this->typeService = new TypeService();
        $this->classService = new ClassService(
            $attributeService,
            new PropertyService($attributeService, $this->typeService)
        );
    }

    /**
     * Class to regex (just for check you self)
     *
     * @param class-string $className
     */
    public function classToRegex(string $className): string
    {
        $reflectionClass = new ReflectionClass($className);

        return $this->classService->getRegexWithFlags($reflectionClass);
    }

    /**
     * Get one object from text
     *
     * @template T of object
     * @param class-string<T> $className
     * @return T|null
     */
    public function one(string $className, string $text): mixed
    {
        $reflectionClass = new ReflectionClass($className);
        $regex = $this->classService->getRegexWithFlags($reflectionClass);

        preg_match($regex, $text, $matches);
        if (count($matches) === 0) {
            return null;
        }

        return $this->objectFromMatches($reflectionClass, $matches);
    }

    /**
     * Get many object from text
     *
     * @template T of object
     * @param class-string<T> $className
     * @return T[]
     */
    public function many(string $className, string $text): array
    {
        $reflectionClass = new ReflectionClass($className);
        $regex = $this->classService->getRegexWithFlags($reflectionClass);

        preg_match_all($regex, $text, $matches);
        if (count($matches) === 0) {
            return [];
        }

        $result = [];
        foreach (array_keys($matches[0]) as $key) {
            $match = array_map(static fn (array $group) => $group[$key], $matches);

            $result[] = $this->objectFromMatches($reflectionClass, $match);
        }

        return $result;
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @param string[] $matches
     * @return T
     */
    private function objectFromMatches(ReflectionClass $reflectionClass, array $matches): mixed
    {
        $arguments = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $name = $property->getName();
            /** @var ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type */
            $type = $property->getType();

            if (isset($matches[$name])) {
                switch (true) {
                    case $this->typeService->equalsType($type, 'int'):
                        $arguments[$name] = (int) $matches[$name];
                        break;
                    case $this->typeService->equalsType($type, 'float'):
                        $arguments[$name] = (float) $matches[$name];
                        break;
                    case $this->typeService->equalsType($type, 'bool'):
                        $arguments[$name] = (bool) $matches[$name];
                        break;
                    case class_exists($type->getName()):
                        if (empty($matches[$name])) {
                            $arguments[$name] = null;
                        } else {
                            $arguments[$name] = $this->objectFromMatches(new ReflectionClass($type->getName()), $matches);
                        }
                        break;
                    default:
                        $arguments[$name] = $matches[$name];
                        break;
                }
            } else {
                $arguments[$name] = null;
            }
        }
        /** @var T $t */
        $t = $reflectionClass->newInstanceArgs($arguments);

        return $t;
    }
}
