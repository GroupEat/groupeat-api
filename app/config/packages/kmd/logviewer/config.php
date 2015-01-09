<?php

return [

    'base_url'   => 'logs',
    'filters'    => [
        'global' => ['before' => 'admin'],
        'view'   => [],
        'delete' => [],
    ],
    'log_dirs'   => ['app' => storage_path('logs')],
    'log_order'  => 'asc', // Change to 'desc' for the latest entries first
    'per_page'   => 20,
    'view'       => 'logviewer::viewer',
    'p_view'     => 'pagination::slider'

];
