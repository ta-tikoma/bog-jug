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

            if ($this->typeService->isArray($property->getType())) {
            } else {
                if (isset($matches[$name])) {
                    $arguments[$name] = $matches[$name];
                } else {
                    $arguments[$name] = null;
                }
            }
        }


        return $reflectionClass->newInstanceArgs($arguments);
    }
}
