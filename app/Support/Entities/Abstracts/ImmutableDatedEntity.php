<?php
namespace Groupeat\Support\Entities\Abstracts;

abstract class ImmutableDatedEntity extends Entity
{
    public $timestamps = false;

    protected $dates = [self::CREATED_AT];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!$this->exists) {
            $createdAtField = self::CREATED_AT;
            $this->$createdAtField = $this->freshTimestamp();
        }
    }
}
