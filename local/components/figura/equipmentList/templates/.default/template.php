<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>

<?php
// Фильтр
$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
    'FILTER_ID' => $arResult['FILTER_ID'],
    'GRID_ID' => $arResult['GRID_ID'],
    'FILTER' => [
        ['id' => 'UF_NAME', 'name' => 'Название', 'type' => 'string'],
        [
            'id' => 'UF_TYPE',
            'name' => 'Тип оборудования',
            'type' => 'list',
            'items' => [
                '' => 'Все',
                'Роутер' => 'Роутер',
                'Ноутбук' => 'Ноутбук',
                'Монитор' => 'Монитор',
            ]
        ],
    ],
    'ENABLE_LIVE_SEARCH' => true,
    'ENABLE_LABEL' => true,
]);

// Грид
$columns = [
    ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false],
    ['id' => 'UF_NAME', 'name' => 'Название', 'sort' => 'UF_NAME', 'default' => true],
    ['id' => 'UF_TYPE', 'name' => 'Тип', 'sort' => 'UF_TYPE', 'default' => true],
    ['id' => 'UF_CREATED_TIME', 'name' => 'Дата создания', 'sort' => 'UF_CREATED_TIME', 'default' => true],
];

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $arResult['GRID_ID'],
    'COLUMNS' => $columns,
    'ROWS' => $arResult['ROWS'],
    'AJAX_MODE' => 'N',
    'SHOW_ROW_CHECKBOXES' => false,
    'SHOW_GRID_SETTINGS_MENU' => true,
    'SHOW_PAGINATION' => false,
    'TOTAL_ROWS_COUNT' => count($arResult['ROWS']),
]);