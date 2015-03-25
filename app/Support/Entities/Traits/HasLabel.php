<?php
namespace Groupeat\Support\Entities\Traits;

use Groupeat\Support\Exceptions\NotFound;

trait HasLabel
{
    private $labelRules = 'required|string';

    /**
     * @param string $label
     *
     * @return static or null if not found
     */
    public static function findByLabel($label)
    {
        return static::where('label', $label)->first();
    }

    /**
     * @param string $label
     *
     * @return static
     */
    public static function findByLabelOrFail($label)
    {
        $entity = static::findByLabel($label);

        if (is_null($entity)) {
            throw new NotFound(
                'unexistingLabel',
                "Cannot find $entity ".class_basename(static::class)." with label $label."
            );
        }

        return $entity;
    }
}
