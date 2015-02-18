<?php namespace Groupeat\Support\Entities;

use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Presenters\Presenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Robbo\Presenter\PresentableInterface;
use Validator;

abstract class Entity extends Model implements PresentableInterface {

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

    /**
     * @var array
     */
    protected $failedRules;


    public static function findOrFail($id, $columns = ['*'])
    {
        $model = static::find($id, $columns);
        $shortClassName = removeNamespaceFromClassName(static::CLASS);

        if (is_null($model))
        {
            throw new NotFound(
                lcfirst($shortClassName).'NotFound',
                $shortClassName." #$id does not exist."
            );
        }

        return $model;
    }

    protected static function boot()
    {
        static::saving(function(Entity $entity)
        {
            if (!static::$skipValidation)
            {
                if (!$entity->validate())
                {
                    throw new UnprocessableEntity(
                        $entity->getFailedRules(),
                        "Cannot save {$entity->toShortString()}."
                    );
                }
            }
        });

        parent::boot();
    }

    protected static function throwNotFoundException()
    {
        $shortClass = removeNamespaceFromClassName(static::class);

        throw new NotFound(
            lcfirst($shortClass).'NotFound',
            $shortClass." not found."
        );
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
        $this->failedRules = $validator->failed();
        $this->validationErrors = $validator->messages();

        return $isValid;
    }

    /**
     * @return array
     */
    public function getFailedRules()
    {
        return $this->failedRules;
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
     * @return string
     */
    public function toShortString()
    {
        $str = lcfirst(getClassNameWithoutNamespace($this));

        if ($id = $this->getKey())
        {
            $str .= ' #'.$id;
        }
        else
        {
            $str = 'new '.$str;
        }

        return $str;
    }

    /**
     * @return Presenter
     */
    public function getPresenter()
    {
        $class = str_replace('Entities', 'Presenters', static::class);

        if (class_exists($class))
        {
            return new $class($this);
        }

        return new Presenter($this);
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

    protected function getIdAttribute()
    {
        return isset($this->attributes['id']) ? (string) $this->attributes['id'] : null;
    }

    /**
     * @param string $name Name of the relation
     * @param Entity $relatedEntity
     *
     * @return $this
     */
    protected function setPolymorphicAttribute($name, Entity $relatedEntity)
    {
        $id = $relatedEntity->getKey();

        if (empty($id))
        {
            $message = 'Cannot set polymorphic relation '
                . $relatedEntity->toShortString().' on '
                . $this->toShortString().'.';

            throw new Exception(
                'polymorphicRelationCannotBeSet',
                $message
            );
        }

        $this->$name()->associate($relatedEntity);

        return $this;
    }

}
