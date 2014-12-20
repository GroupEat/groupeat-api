<?php namespace Groupeat\Support\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

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

    public function getErrors()
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
    public function getTable()
    {
        $migration = $this->getRelatedMigration();

        return $migration::TABLE;
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

}
