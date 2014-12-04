<?php namespace Groupeat\Database;

trait TableNameGetter {

    protected $tableName;

    public function getTableName()
    {
        if (!empty($this->tableName))
        {
            return $this->tableName;
        }

        if (!empty(static::MODEL_CLASS))
        {
            $modelClass = static::MODEL_CLASS;

            return with(new $modelClass)->getTable();
        }
    }

}
