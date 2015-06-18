<?php
namespace Bcremer\Serialize\Tests\Objects;

class ExtendedWithOverwrittenPrivateObject extends SimpleObject
{
    private $baz;

    private $foo;

    public function __construct($baz, $foo)
    {
        parent::__construct("fooValue", "barValue");
        $this->baz = $baz;
        $this->foo = $foo;
    }

    public function getBaz()
    {
        return $this->baz;
    }

    public function getExtendedFoo()
    {
        return $this->foo;
    }
}
