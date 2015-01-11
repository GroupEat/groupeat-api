<?php

exec('rm '.__DIR__.'/../_data/testing.sqlite');
exec('cp '.__DIR__ .'/../_data/setup.sqlite '.__DIR__.'/../_data/testing.sqlite');
