<?php

Artisan::add(new BuildAssetsCommand);
Artisan::add(new PublishAllAssetsCommand);
Artisan::add(new PullCommand);
Artisan::add(new DebugCodeceptionOnShippable);
