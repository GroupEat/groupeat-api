<?php
namespace Groupeat\Support\Pipeline;

use Groupeat\Support\Commands\Abstracts\Command;
use Illuminate\Database\DatabaseManager;

class ExecuteCommandInDbTransaction
{
    private $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function handle(Command $command, $next)
    {
        return $this->db->connection()->transaction(function () use ($command, $next) {
            return $next($command);
        });
    }
}
