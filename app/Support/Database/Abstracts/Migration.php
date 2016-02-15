<?php
namespace Groupeat\Support\Database\Abstracts;

use Groupeat\Support\Exceptions\Exception;
use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Groupeat\Support\Entities\Abstracts\Entity;
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

    public function getTable(): string
    {
        if (!empty($this->table)) {
            return $this->table;
        }

        return $this->getEntity()->getTable();
    }

    protected function getTableFor(string $migrationClassWithNamespace): string
    {
        return (new $migrationClassWithNamespace)->getTable();
    }

    protected function getEntity(): Entity
    {
        if (!empty($this->entity)) {
            $className = $this->entity;

            return new $className;
        }

        throw new Exception('notImplemented', "The getEntity method should be defined by inheritance");
    }
}
