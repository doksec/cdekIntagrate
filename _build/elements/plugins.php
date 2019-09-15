<?php

return [
    'CdekIntgratePlugin' => [
        'file' => 'cdekintgrate',
        'description' => 'Плагин интеграции СДЭК',
        'events' => [
            'msOnManagerCustomCssJs' => [],
            'msOnCreateOrder' => [],
            'msOnChangeOrderStatus' => [],
        ],
    ],
];