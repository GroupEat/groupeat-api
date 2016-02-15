<?php
namespace Groupeat\Support\Entities\Traits;

use Groupeat\Support\Exceptions\NotFound;

trait HasLabel
{
    private $labelRules = 'required|string';

    /**
     * @return static|null if not found
     */
    public static function findByLabel(string $label)
    {
        return static::where('label', $label)->first();
    }

    /**
     * @return static
     */
    public static function findByLabelOrFail(string $label)
    {
        $entity = static::findByLabel($label);

        if (is_null($entity)) {
            throw new NotFound(
                'unexistingLabel',
                "Cannot find ".class_basename(static::class)." with label $label."
            );
        }

        return $entity;
    }
}
