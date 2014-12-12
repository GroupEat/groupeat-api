<?php namespace Groupeat\Core\Support\Database;

trait TableGetter {

    protected $table;

    public function getTable()
    {
        if (!empty($this->table))
        {
            return $this->table;
        }

        if (method_exists($this, 'getModel'))
        {
            return $this->getModel()->getTable();
        }
    }
}
