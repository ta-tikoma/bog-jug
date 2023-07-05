<?php

declare(strict_types=1);

namespace BogJug\Services;

use BogJug\Attributes\Count\Between;
use BogJug\Attributes\Count\CountOrMore;
use BogJug\Attributes\Count\Exactly;
use BogJug\Attributes\Count\OneOrMore;
use BogJug\Attributes\Count\ZeroOrMore;
use BogJug\Attributes\Count\ZeroOrOne;
use BogJug\Attributes\Regex\After;
use BogJug\Attributes\Regex\Before;
use BogJug\Attributes\Regex\Group;
use Exception;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

final class PropertyService
{
    public function __construct(
        private AttributeService $attributeService,
        private TypeService $typeService,
    ) {
    }

    public function regexGroupFromProperties(ReflectionClass $reflectionClass): string
    {
        $regex = '';
        $properites = $reflectionClass->getProperties();
        if (count($properites) === 0) {
            throw new Exception("{$reflectionClass->getName()} has not properties");
        }

        foreach ($properites as $property) {
            $regex .= $this->regexGroupFromProperty($reflectionClass, $property);
        }

        return $regex;
    }

    private function regexGroupFromProperty(ReflectionClass $reflectionClass, ReflectionProperty $property): string
    {
        $method = 'getGroupOptionsFor' . ucfirst($property->getName());

        $result = $this->getBeforeRegex($property)
            . "(?<{$property->getName()}>"
            . ($reflectionClass->hasMethod($method)
                ? call_user_func([$reflectionClass->getName(), $method])
                : $this->collectGroupOptions($property))
            . ')'
            . $this->getCountRegex($property)
            . $this->getAfterRegex($property)
            //
        ;

        return $result;
    }

    private function getBeforeRegex(ReflectionProperty $reflectionProperty): string
    {
        /** @var Before|null $before */
        $before = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, Before::class);
        if ($before === null) {
            return '';
        }

        return $before->regex;
    }

    private function getAfterRegex(ReflectionProperty $reflectionProperty): string
    {
        /** @var After|null $after */
        $after = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, After::class);
        if ($after === null) {
            return '';
        }

        return $after->regex;
    }

    /**
     * @example return 'su|mo|tu|we|th|fr|sa'
     */
    private function collectGroupOptions(ReflectionProperty $reflectionProperty): string
    {
        /** @var ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type */
        $type = $reflectionProperty->getType();
        $reflectionClass = $this->typeService->getClassOfType($type);
        if ($reflectionClass !== null) {
            return $this->regexGroupFromProperties($reflectionClass);
        }

        $attributes = $reflectionProperty->getAttributes(Group::class);
        if (count($attributes) === 0) {
            throw new Exception("{$reflectionProperty->getName()} has not Group attribute");
        }

        $options = [];
        foreach ($attributes as $attribute) {
            /** @var Group $regexGroup */
            $regexGroup = $attribute->newInstance();
            $options[] = $regexGroup->regex;
        }

        return implode('|', $options);
    }


    private function getCountRegex(ReflectionProperty $reflectionProperty): string
    {
        /** @var Between|null $between */
        $between = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, Between::class);
        if ($between !== null) {
            return "\{$between->min,$between->max\}";
        }

        /** @var CountOrMore|null $countOrMore */
        $countOrMore = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, CountOrMore::class);
        if ($countOrMore !== null) {
            return "\{$countOrMore->count,\}";
        }

        /** @var Exactly|null $exactly */
        $exactly = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, Exactly::class);
        if ($exactly !== null) {
            return "\{$exactly->count\}";
        }

        /** @var OneOrMore|null $oneOrMore */
        $oneOrMore = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, OneOrMore::class);
        if ($oneOrMore !== null) {
            return '+';
        }

        /** @var ZeroOrMore|null $zeroOrMore */
        $zeroOrMore = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, ZeroOrMore::class);
        if ($zeroOrMore !== null) {
            return '*';
        }

        /** @var ZeroOrOne|null $zeroOrOne */
        $zeroOrOne = $this->attributeService->getAttributeInstanseOrNull($reflectionProperty, ZeroOrOne::class);
        if ($zeroOrOne !== null) {
            return '?';
        }

        /** @var ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type */
        $type = $reflectionProperty->getType();

        if ($type === null) {
            return '?';
        }

        if ($type->allowsNull()) {
            return '?';
        }

        return '{1}';
    }
}

