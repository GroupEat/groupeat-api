<?php
namespace Groupeat\Support\Pipeline;

use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Database\DatabaseManager;

class ExecuteJobInDbTransaction
{
    private $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function handle(Job $job, $next)
    {
        return $this->db->connection()->transaction(function () use ($job, $next) {
            return $next($job);
        });
    }
}
