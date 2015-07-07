<?php

$target = $argv[1];
$branch = $argv[2];

$lastCommitMessage = exec('git log -1 HEAD --pretty=format:%s');

if (stristr($lastCommitMessage, 'skip deploy') === false) {
    echo "Downloading Rocketeer\n";

    system('wget http://rocketeer.autopergamene.eu/versions/rocketeer.phar -O rocketeer.phar');
    chmod('rocketeer.phar', 0755);

    echo "Deploying\n";
    system("./rocketeer.phar deploy --on=$target --branch=$branch");

    echo "Removing Rocketeer executable\n";
    unlink('./rocketeer.phar');
} else {
    echo "Deployment cancelled\n";
}
