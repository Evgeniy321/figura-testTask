<?php

use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'main',
    'OnProlog',
    'addCustomCssForUser5'
);

function addCustomCssForUser5()
{
    global $USER;

    if (is_object($USER) && (int)$USER->GetID() === 5) {
        \Bitrix\Main\Page\Asset::getInstance()->addCss('/local/css/style.css');
    }
}