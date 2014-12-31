<?php namespace Groupeat\Support\Entities;

use Illuminate\Database\Eloquent\Model;
use Validator;

abstract class Entity extends Model {

    /**
     * Can be set to true for easier seeding
     *
     * @var bool Indicate if the entity can be saved without being valid
     */
    public static $skipValidation = false;

    protected $validationErrors;


    protected static function boot()
    {
        Model::boot();

        static::saving(function($entity)
        {
            if (!static::$skipValidation)
            {
                return $entity->validate();
            }
        });
    }

    public function validate()
    {
        $validator = Validator::make($this->attributes, $this->getRules());
        $isValid = $validator->passes();
        $this->validationErrors = $validator->messages();

        return $isValid;
    }

    public function forceSave(array $options = [])
    {
        static::$skipValidation = true;

        $status = $this->save($options);

        static::$skipValidation = false;

        return $status;
    }

    public function errors()
    {
        return $this->validationErrors;
    }

    /**
     * @return array The rules that the entity must match
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
     * @param $fieldName
     *
     * @return string The field name preceded by the the table name
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
        $entityClass = get_class($this);
        $temp = str_replace('\\Entities\\', '\\Migrations\\', $entityClass);
        $migrationClass = str_plural($temp).'Migration';

        return new $migrationClass;
    }

    protected function setPolymorphicAttribute($name, Entity $relatedEntity)
    {
        $this->setRelation($name, $relatedEntity);
        $type = $name.'_type';
        $id = $name.'_id';
        $this->$type = get_class($relatedEntity);
        $this->$id = $relatedEntity->id;

        return $this;
    }

}
