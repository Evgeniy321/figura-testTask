<?php
// /local/ajax/equipment_tab.php

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION;

// Правильный путь к параметрам с учетом лога
$dealId = 0;
if (isset($_REQUEST['PARAMS']['params']['DEAL_ID'])) {
    $dealId = (int)$_REQUEST['PARAMS']['params']['DEAL_ID'];
}

// Для проверки (потом можно удалить)
myDebugLog($dealId, 'dealId в equipment_tab.php ПОСЛЕ ИСПРАВЛЕНИЯ');

$APPLICATION->ShowHead();

$APPLICATION->IncludeComponent(
    'figura:equipmentList',
    '.default',
    [
        'DEAL_ID' => $dealId,
    ],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');