<?php
namespace Groupeat\Support\Entities\Abstracts;

abstract class ImmutableDatedEntity extends Entity
{
    public $timestamps = false;

    protected $dates = [Entity::CREATED_AT];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!$this->exists) {
            $createdAtField = Entity::CREATED_AT;
            $this->$createdAtField = $this->freshTimestamp();
        }
    }
}
