<?php

declare(strict_types=1);

namespace BogJug;

use BogJug\Services\AttributeService;
use BogJug\Services\ClassService;
use BogJug\Services\PropertyService;
use BogJug\Services\TypeService;
use ReflectionClass;

final class BogJug
{
    private ClassService $classService;

    public function __construct()
    {
        $attributeService = new AttributeService();
        $this->classService = new ClassService(
            $attributeService,
            new PropertyService($attributeService, new TypeService())
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
     * @template T
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

        $arguments = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $name = $property->getName();

            if (isset($matches[$name])) {
                $arguments[$name] = $matches[$name];
            } else {
                $arguments[$name] = null;
            }
        }

        /** @var T $t */
        $t = $reflectionClass->newInstanceArgs($arguments);

        return $t;
    }

    /**
     * Get many object from text
     *
     * @template T
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
            $arguments = [];
            foreach ($reflectionClass->getProperties() as $property) {
                $name = $property->getName();

                if (isset($matches[$name][$key])) {
                    $arguments[$name] = $matches[$name][$key];
                } else {
                    $arguments[$name] = null;
                }
            }
            /** @var T $t */
            $t = $reflectionClass->newInstanceArgs($arguments);

            $result[] = $t;
        }

        return $result;
    }
}
