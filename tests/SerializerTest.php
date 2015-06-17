<?php
namespace Bcremer\Serialize\Tests;

use Bcremer\Serialize\Serializer;
use Bcremer\Serialize\Tests\Objects\ExtendedObject;
use Bcremer\Serialize\Tests\Objects\SimpleObject;
use Doctrine\Instantiator\Instantiator;
use Doctrine\Instantiator\InstantiatorInterface;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_instanciated()
    {
        $instantiator = $this->prophesize(InstantiatorInterface::class)->reveal();

        $this->assertInstanceOf(Serializer::class, new Serializer($instantiator));
    }

    /**
     * @test
     */
    public function it_can_serialize_simple_object()
    {
        $instantiator = $this->prophesize(InstantiatorInterface::class)->reveal();

        $SUT = new Serializer($instantiator);

        $object = new SimpleObject("fooValue", "barValue");

        $expected = [
            'className' => 'Bcremer\Serialize\Tests\Objects\SimpleObject',
            'properties' => [
                'foo' => 'fooValue',
                'bar' => 'barValue',
            ]
        ];

        $this->assertSame($expected, $SUT->serialize($object));
    }

    /**
     * @test
     */
    public function it_can_unserialize_simple_object()
    {
        $instantiator = new Instantiator();

        $SUT = new Serializer($instantiator);

        $serializedObject = [
            'className' => 'Bcremer\Serialize\Tests\Objects\SimpleObject',
            'properties' => [
                'foo' => 'fooValue',
                'bar' => 'barValue',
            ]
        ];

        $expected = new SimpleObject("fooValue", "barValue");

        $object = $SUT->unserialize($serializedObject);

        $this->assertEquals(serialize($expected), serialize($object));
    }

    /**
     * @test
     */
    public function it_can_unserialize_extended_object()
    {
        $instantiator = new Instantiator();

        $SUT = new Serializer($instantiator);

        $serializedObject = [
            'className' => 'Bcremer\Serialize\Tests\Objects\ExtendedObject',
            'properties' => [
                'baz' => 'bazValue',
            ],
            'parentProperties' => [
                'foo' => 'fooValue',
                'bar' => 'barValue',
            ],
        ];

        /** @var ExtendedObject $object */
        $object = $SUT->unserialize($serializedObject);
        $expected = new ExtendedObject("bazValue");

        $this->assertEquals(serialize($expected), serialize($object));
    }

    /**
     * @test
     */
    public function it_can_serialize_extended_object()
    {
        $instantiator = $this->getMock(InstantiatorInterface::class);

        $SUT = new Serializer($instantiator);

        $object = new ExtendedObject("bazValue");

        $expected = [
            'className' => 'Bcremer\Serialize\Tests\Objects\ExtendedObject',
            'properties' => [
                'baz' => 'bazValue',
            ],
            'parentProperties' => [
                'foo' => 'fooValue',
                'bar' => 'barValue',
            ],
        ];

        $this->assertSame($expected, $SUT->serialize($object));
    }

    /**
     * @test
     */
    public function it_can_restore_serialized_object()
    {
        $instantiator = new Instantiator();
        $SUT = new Serializer($instantiator);

        $object = new ExtendedObject("bazValue");

        $serialized = $SUT->serialize($object);

        $restoredObject = $SUT->unserialize($serialized);

        assert($restoredObject == $object);

        $this->assertSame(serialize($object), serialize($restoredObject));
    }
}
