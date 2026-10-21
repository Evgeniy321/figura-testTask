<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\EventResult;

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

    // ID смарт-процесса
    $mySmartProcessTypeId = 1038;

    if ($entityTypeID !== $mySmartProcessTypeId) {
        return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs]);
    }

    // Получаем ID связанной сделки
    $dealId = 0;
    $factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($entityTypeID);

    if ($factory) {
        $item = $factory->getItem($entityID);
        if ($item) {
            $dealId = (int)$item->get('PARENT_ID_2');
            // myDebugLog($dealId, 'dealId из карточки'); // Раскомментируйте при необходимости
        }
    }

    // Добавляем вкладку через loader
    $tabs[] = [
        'id'   => 'equipment_tab',
        'name' => 'Оборудование',
        'loader' => [
            'serviceUrl' => '/local/ajax/equipment_tab.php',
            'componentData' => [
                'params' => [
                    'DEAL_ID' => $dealId
                ]
            ]
        ]
    ];

    return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs]);
}