<?php namespace Groupeat\Support\Entities;

use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Validator;

abstract class Entity extends Model {

    /**
     * Can be set to true for easier seeding.
     *
     * @var bool Indicate if the entity can be saved without being valid
     */
    public static $skipValidation = false;

    /**
     * @var MessageBag
     */
    protected $validationErrors;


    protected static function boot()
    {
        static::saving(function(Entity $entity)
        {
            if (!static::$skipValidation)
            {
                if (!$entity->validate())
                {
                    $entityString = getClassNameWithoutNamespace($entity).' #'.$entity->id;

                    throw new BadRequest("Cannot save $entityString.", $entity->errors());
                }
            }
        });

        parent::boot();
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->validationErrors = new MessageBag;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $validator = Validator::make($this->attributes, $this->getRules());
        $isValid = $validator->passes();
        $this->validationErrors = $validator->messages();

        return $isValid;
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    public function forceSave(array $options = [])
    {
        static::$skipValidation = true;

        $status = $this->save($options);

        static::$skipValidation = false;

        return $status;
    }

    /**
     * @return MessageBag
     */
    public function errors()
    {
        return $this->validationErrors;
    }

    /**
     * @return array Rules that the entity must match
     */
    abstract public function getRules();

    /**
     * @return string Table name of the entity
     */
    public static function table()
    {
        return (new static)->getTable();
    }

    /**
     * @return string Table name of the entity
     */
    public function getTable()
    {
        $migration = $this->getRelatedMigration();

        return $migration::TABLE;
    }

    /**
     * @param string $fieldName
     *
     * @return string Field name preceded by the the table name
     */
    public function getTableField($fieldName)
    {
        return $this->getTable().'.'.$fieldName;
    }

    /**
     * @return \Groupeat\Support\Database\Migration
     */
    protected function getRelatedMigration()
    {
        $temp = str_replace('\\Entities\\', '\\Migrations\\', static::class);
        $migrationClass = str_plural($temp).'Migration';

        return new $migrationClass;
    }

    /**
     * @param string $name Name of the relation
     * @param Entity $relatedEntity
     *
     * @return $this
     */
    protected function setPolymorphicAttribute($name, Entity $relatedEntity)
    {
        $this->setRelation($name, $relatedEntity);
        $type = "{$name}_type";
        $id = "{$name}_id";
        $this->$type = get_class($relatedEntity);
        $this->$id = $relatedEntity->id;

        return $this;
    }

}
