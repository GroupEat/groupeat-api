<?php

$target = $argv[1];
$branch = $argv[2];

$lastCommitMessage = exec('git log -1 HEAD --pretty=format:%s');

if (stristr($lastCommitMessage, 'skip deploy') === false) {
    echo "Deploying\n";
    system("rocketeer deploy --on=$target --branch=$branch");
} else {
    echo "Deployment cancelled\n";
}
