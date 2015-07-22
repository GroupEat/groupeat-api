<?php
namespace Groupeat\Support\Database\Abstracts;

use Groupeat\Support\Exceptions\Exception;
use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends LaravelMigration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Implemented by inheritance
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->getTable());
    }

    /**
     * @return string The table name
     */
    public function getTable()
    {
        if (!empty($this->table)) {
            return $this->table;
        }

        return $this->getEntity()->getTable();
    }

    /**
     * @param string $migration The other migration full class path
     *
     * @return string The corresponding table name
     */
    protected function getTableFor($migration)
    {
        return (new $migration)->getTable();
    }

    /**
     * @return \Groupeat\Support\Entities\Abstracts\Entity The related entity
     */
    protected function getEntity()
    {
        if (!empty($this->entity)) {
            $className = $this->entity;

            return new $className;
        }

        throw new Exception('notImplemented', "The getEntity method should be defined by inheritance");
    }
}
