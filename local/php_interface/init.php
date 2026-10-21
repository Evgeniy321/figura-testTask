<?php
// /local/php_interface/init.php

use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'main',
    'OnProlog',
    'addCustomCssForUser5'
);

function addCustomCssForUser5()
{
    global $USER;

    // Теперь $USER гарантированно существует
    if (is_object($USER) && (int)$USER->GetID() === 5) {
        \Bitrix\Main\Page\Asset::getInstance()->addCss('/local/css/style.css');
    }
}

EventManager::getInstance()->addEventHandler(
    'crm',
    'onEntityDetailsTabsInitialized',
    'addEquipmentTabToSmartProcess'
);

function addEquipmentTabToSmartProcess($event)
{
    $tabs = $event->getParameter('tabs');
    $entityID = (int)$event->getParameter('entityID');
    $entityTypeID = (int)$event->getParameter('entityTypeID');

    // Укажите ваш реальный ID смарт-процесса
    $mySmartProcessTypeId = 1038;

    if ($entityTypeID !== $mySmartProcessTypeId) {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['tabs' => $tabs]);
    }

    // Получаем ID связанной сделки
    $dealId = 0;
    $factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($entityTypeID);
    if ($factory) {
        $item = $factory->getItem($entityID);
        if ($item) {
            $dealId = (int)$item->get('PARENT_ID_2');
            myDebugLog($dealId, 'dealId из карточки');
        }
    }
    // Добавляем вкладку через loader
    $tabs[] = [
        'id'   => 'equipment_tab',
        'name' => 'Оборудование',
        'loader' => [
            'serviceUrl' => '/local/ajax/equipment_tab.php', // URL ajax-файла
            'componentData' => [
                'params' => [
                    'DEAL_ID' => $dealId
                ]
            ]
        ]
    ];

    return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ['tabs' => $tabs]);
}

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\Event;

$eventManager = EventManager::getInstance();

// Регистрируем обработчики для HL-блока "Equipment"
// Важно: префикс события совпадает с кодом сущности HL-блока
$eventManager->addEventHandler('', 'EquipmentOnAfterAdd', 'recalculateDealEquipmentCount');
$eventManager->addEventHandler('', 'EquipmentOnAfterUpdate', 'recalculateDealEquipmentCount');
$eventManager->addEventHandler('', 'EquipmentOnAfterDelete', 'recalculateDealEquipmentCount');

function recalculateDealEquipmentCount(Event $event)
{
    // 1. Получаем поля элемента HL-блока из события
    $arFields = $event->getParameter('fields');
    $dealId = (int)($arFields['UF_DEAL_ID'] ?? 0);

    if ($dealId <= 0) {
        return;
    }

    // 2. Подсчитываем актуальное количество оборудования для этой сделки
    Loader::includeModule('highloadblock');
    $hlblock = HighloadBlockTable::getList([
        'filter' => ['=NAME' => 'Equipment']
    ])->fetch();

    if (!$hlblock) {
        return;
    }

    $entity = HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    $count = $entityClass::getCount([
        '=UF_DEAL_ID' => $dealId
    ]);

    // 3. Обновляем пользовательское поле в сделке
    $GLOBALS['USER_FIELD_MANAGER']->Update('CRM_DEAL', $dealId, [
        'UF_CRM_1792574496136' => $count
    ]);
}

function myDebugLog($data, $label = '') {
    $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/debug.log';
    $message = date('Y-m-d H:i:s') . ' | ' . $label . ' | ' . print_r($data, true) . "\n";
    file_put_contents($logPath, $message, FILE_APPEND);
}