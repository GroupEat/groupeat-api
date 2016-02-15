<?php
namespace Groupeat\Support\Entities\Abstracts;

use Groupeat\Support\Database\Abstracts\Migration;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Presenters\Presenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Robbo\Presenter\PresentableInterface;
use Validator;

abstract class Entity extends Model implements PresentableInterface
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

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
        $model = static::query()->find($id, $columns);
        $shortClassName = class_basename(static::CLASS);

        if (is_null($model)) {
            throw new NotFound(
                lcfirst($shortClassName).'NotFound',
                $shortClassName." #$id does not exist."
            );
        }

        return $model;
    }

    public static function table()
    {
        return (new static)->getTable();
    }

    protected static function boot()
    {
        static::saving(function (Entity $entity) {
            if (!static::$skipValidation) {
                if (!$entity->validate()) {
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
        $shortClass = class_basename(static::class);

        throw new NotFound(
            lcfirst($shortClass).'NotFound',
            $shortClass." not found."
        );
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->validationErrors = new MessageBag;
    }

    public function validate(): bool
    {
        $validator = Validator::make($this->attributes, $this->getRules());
        $isValid = $validator->passes();
        $this->failedRules = $validator->failed();
        $this->validationErrors = $validator->messages();

        return $isValid;
    }

    public function getFailedRules(): array
    {
        return $this->failedRules;
    }

    public function forceSave(array $options = []): bool
    {
        static::$skipValidation = true;

        $status = $this->save($options);

        static::$skipValidation = false;

        return $status;
    }

    public function errors(): MessageBag
    {
        return $this->validationErrors;
    }

    /**
     * @return array Rules that the entity must match
     */
    abstract public function getRules();

    public function getTableField(string $fieldName): string
    {
        return $this->getTable().'.'.$fieldName;
    }

    public function getRawTableField(string $fieldName): string
    {
        return '"'.$this->getTable().'"."'.$fieldName.'"';
    }

    public function toShortString(): string
    {
        $str = lcfirst(class_basename($this));

        if ($id = $this->getKey()) {
            $str .= ' #'.$id;
        } else {
            $str = 'new '.$str;
        }

        return $str;
    }

    public function getPresenter(): Presenter
    {
        $class = str_replace('Entities', 'Presenters', static::class) . 'Presenter';

        if (class_exists($class)) {
            return new $class($this);
        }

        return new Presenter($this);
    }

    public function getForeignKey()
    {
        return lcfirst(class_basename($this).'Id');
    }

    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            list(, $caller) = debug_backtrace(false, 2);

            $relation = $caller['function'];
        }

        if (is_null($foreignKey)) {
            $foreignKey = lcfirst($relation).'Id';
        }

        return parent::belongsTo($related, $foreignKey, $otherKey, $relation);
    }

    protected function getMorphs($name, $type, $id)
    {
        $type = $type ?: $name.'Type';

        $id = $id ?: $name.'Id';

        return [$type, $id];
    }

    protected function getRelatedMigration(): Migration
    {
        $temp = str_replace('\\Entities\\', '\\Migrations\\', static::class);
        $migrationClass = str_plural($temp).'Migration';

        return new $migrationClass;
    }

    protected function getIdAttribute()
    {
        return isset($this->attributes['id']) ? (string) $this->attributes['id'] : null;
    }
}
