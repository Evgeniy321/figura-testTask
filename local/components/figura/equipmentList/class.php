<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\UI\Filter\Options;
use Bitrix\Highloadblock\HighloadBlockTable;

class EquipmentListComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('highloadblock');

        // 1. ID сделки (из параметра или запроса)
        $dealId = (int)($this->arParams['DEAL_ID'] ?? $_REQUEST['deal_id'] ?? 0);

        // 2. Инициализация фильтра
        $filterId = 'EQUIPMENT_FILTER_' . $dealId;
        $filterOption = new Options($filterId);
        $filterData = $filterOption->getFilter();

        // 3. Формирование фильтра для HL-блока
        $hlFilter = ['=UF_DEAL_ID' => $dealId];

        // Добавляем фильтрацию по названию (если задана)
        if (!empty($filterData['UF_NAME'])) {
            $hlFilter['%UF_NAME'] = $filterData['UF_NAME'];
        }
        // Добавляем фильтрацию по типу (если задан)
        if (!empty($filterData['UF_TYPE'])) {
            $hlFilter['=UF_TYPE'] = $filterData['UF_TYPE'];
        }

        // 4. Получение данных из HL-блока
        $hlblock = HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'Equipment'] // имя вашего HL-блока
        ])->fetch();

        $rows = [];
        if ($hlblock) {
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $entityClass = $entity->getDataClass();

            $items = $entityClass::getList([
                'filter' => $hlFilter,
                'order' => ['UF_CREATED_TIME' => 'DESC'],
                'select' => ['ID', 'UF_NAME', 'UF_TYPE', 'UF_CREATED_TIME', 'UF_DEAL_ID']
            ])->fetchAll();
            myDebugLog($hlFilter, 'Фильтр HL');
            myDebugLog(count($items), 'Количество найденных элементов');
            // 5. Подготовка строк для грида
            foreach ($items as $item) {
                $rows[] = [
                    'data' => [
                        'ID' => $item['ID'],
                        'UF_NAME' => $item['UF_NAME'],
                        'UF_TYPE' => $item['UF_TYPE'],
                        'UF_CREATED_TIME' => $item['UF_CREATED_TIME'],
                    ]
                ];
            }
        }

        $this->arResult['ROWS'] = $rows;
        $this->arResult['FILTER_ID'] = $filterId;
        $this->arResult['GRID_ID'] = 'EQUIPMENT_GRID';
        $this->arResult['DEAL_ID'] = $dealId;

        $this->includeComponentTemplate();
    }
}