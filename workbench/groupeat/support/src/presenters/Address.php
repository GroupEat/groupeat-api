<?php namespace Groupeat\Support\Presenters;

class Address extends Presenter {

    public function __toString()
    {
        $object = $this->object;

        $str = $object->street;

        if (!empty($object->details))
        {
            $str .= " ($object->details)";
        }

        $str .= ', '.$object->city;

        return $str;
    }

}
