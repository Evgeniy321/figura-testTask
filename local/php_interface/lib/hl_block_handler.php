<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\Event;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler('', 'EquipmentOnAfterAdd', 'recalculateDealEquipmentCount');
$eventManager->addEventHandler('', 'EquipmentOnAfterUpdate', 'recalculateDealEquipmentCount');
$eventManager->addEventHandler('', 'EquipmentOnAfterDelete', 'recalculateDealEquipmentCount');

function recalculateDealEquipmentCount(Event $event)
{
    $arFields = $event->getParameter('fields');
    $dealId = (int)($arFields['UF_DEAL_ID'] ?? 0);

    if ($dealId <= 0) {
        return;
    }

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

    // Обновляем пользовательское поле в сделке
    $GLOBALS['USER_FIELD_MANAGER']->Update('CRM_DEAL', $dealId, [
        'UF_CRM_1792574496136' => $count
    ]);
}