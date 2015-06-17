<?php
namespace Bcremer\Serialize;

use Doctrine\Instantiator\InstantiatorInterface;

class Serializer
{
    /**
     * @var InstantiatorInterface
     */
    private $instantiator;

    /**
     * @param InstantiatorInterface $instantiator
     */
    public function __construct(InstantiatorInterface $instantiator)
    {
        $this->instantiator = $instantiator;
    }

    /**
     * @param object $instance
     * @return array
     */
    public function serialize($instance)
    {
        $propertyReader = \Closure::bind(function &($instance) {
            $properties = [];
            foreach ($instance as $key => $value) {
                $properties[$key] = $value;
            }

            return $properties;
        }, null, $instance);

        $result = [
            'className' => get_class($instance),
            'properties' => $propertyReader($instance),
        ];

        $reflectionClass = new \ReflectionClass($instance);
        $parentRreflection = $reflectionClass->getParentClass();

        $parentProperties = [];
        if ($parentRreflection) {
            foreach ($parentRreflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
                $property->setAccessible(true);
                $parentProperties[$property->getName()] = $property->getValue($instance);
            }

            $result['parentProperties'] = $parentProperties;
        }

        return $result;
    }

    /**
     * @param $serializedObject
     * @return object
     */
    public function unserialize($serializedObject)
    {
        $className = $serializedObject['className'];
        $properties = $serializedObject['properties'];

        $instance = $this->instantiator->instantiate($className);

        $propertySetter = function ($instance, array $properties) {
            foreach ($properties as $key => $value) {
                $instance->$key = $value;
            }
        };
        $propertySetter = \Closure::bind($propertySetter, null, $instance);
        $propertySetter($instance, $properties);

        if (isset($serializedObject['parentProperties'])) {
            $parentProperties = $serializedObject['parentProperties'];
            $reflectionClass = new \ReflectionClass($instance);
            $parentRreflection = $reflectionClass->getParentClass();

            foreach ($parentRreflection->getProperties() as $property) {
                if (!isset($parentProperties[$property->getName()])) {
                    continue;
                }

                $property->setAccessible(true);
                $property->setValue($instance, $parentProperties[$property->getName()]);
            }
        }

        return $instance;
    }
}
