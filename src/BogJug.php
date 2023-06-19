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

    private TypeService $typeService;

    public function __construct()
    {
        $attributeService = new AttributeService();
        $this->typeService = new TypeService();
        $this->classService = new ClassService(
            $attributeService,
            new PropertyService(
                $attributeService,
                $this->typeService
            )
        );
    }

    /**
     * Class to regex (just for check you self)
     */
    public function classToRegex(string $className): string
    {
        $reflectionClass = new ReflectionClass($className);

        return $this->classService->getRegex($reflectionClass);
    }

    /**
     * Get one object from text
     */
    public function one(string $className, string $text): mixed
    {
        $reflectionClass = new ReflectionClass($className);
        $regex = $this->classService->getRegex($reflectionClass);

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
                if ($this->typeService->isArray($property->getType())) {
                    $arguments[$name] = [];
                } else {
                    $arguments[$name] = null;
                }
            }
        }


        return $reflectionClass->newInstanceArgs($arguments);
    }

    /**
     * Get many object from text
     */
    public function many(string $className, string $text): array
    {
        $reflectionClass = new ReflectionClass($className);
        $regex = $this->classService->getRegex($reflectionClass);

        preg_match_all($regex, $text, $matches);
        if (count($matches) === 0) {
            return null;
        }

        $result = [];
        foreach (array_keys($matches[0]) as $key) {
            $arguments = [];
            foreach ($reflectionClass->getProperties() as $property) {
                $name = $property->getName();

                if (isset($matches[$name][$key])) {
                    $arguments[$name] = $matches[$name][$key];
                } else {
                    if ($this->typeService->isArray($property->getType())) {
                        $arguments[$name] = [];
                    } else {
                        $arguments[$name] = null;
                    }
                }
            }
            $result[] = $reflectionClass->newInstanceArgs($arguments);
        }

        return $result;
    }
}
