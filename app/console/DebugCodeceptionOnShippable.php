<?php

class DebugCodeceptionOnShippable extends \Groupeat\Support\Console\Command {

    protected $name = 'codeception:debug';
    protected $description = "Temporary command to try to debug codeception";

    public function fire()
    {
        $class = 'Rest';
        $config = [];
        $namespace = '';

        var_dump("\Codeception\Module\\$class");
        var_dump(class_exists("\Codeception\Module\\$class"));
        var_dump(@class_exists("\Codeception\Module\\$class"));

        // try find module under users suite namespace setting
        $className = $namespace.'\\Codeception\\Module\\' . $class;

        var_dump($className);

        if (!@class_exists($className)) {
            // fallback to default namespace
            $className = '\\Codeception\\Module\\' . $class;

            var_dump($className);

            if (!@class_exists($className)) {
                var_dump($class.' could not be found and loaded');
            }
        }
    }

}
