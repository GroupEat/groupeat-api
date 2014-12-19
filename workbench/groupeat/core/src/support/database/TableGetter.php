<?php namespace Groupeat\Core\Support\Database;

trait TableGetter {

    /**
     * @var string The name of the DB table
     */
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
