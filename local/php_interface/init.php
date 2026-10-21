<?php
// /local/php_interface/init.php

use Bitrix\Main\EventManager;

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

function myDebugLog($data, $label = '') {
    $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/debug.log';
    $message = date('Y-m-d H:i:s') . ' | ' . $label . ' | ' . print_r($data, true) . "\n";
    file_put_contents($logPath, $message, FILE_APPEND);
}