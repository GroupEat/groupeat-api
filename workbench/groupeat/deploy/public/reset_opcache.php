<?php

if (function_exists('opcache_reset'))
{
    opcache_reset();
}
else
{
    echo "OPcache is not enabled\n";
}
