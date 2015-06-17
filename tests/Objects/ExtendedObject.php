<?php
namespace Bcremer\Serialize\Tests\Objects;

class ExtendedObject extends SimpleObject
{
    private $baz;

    public function __construct($baz)
    {
        parent::__construct("fooValue", "barValue");
        $this->baz = $baz;
    }

    public function getBaz()
    {
        return $this->baz;
    }
}
