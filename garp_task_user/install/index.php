<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bex\D7dull\ExampleTable;

Loc::loadMessages(__FILE__);

class garp_task_user extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        
        $this->MODULE_ID = 'garp_task_user';
        $this->MODULE_NAME = Loc::getMessage('BEX_D7DULL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BEX_D7DULL_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('BEX_D7DULL_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'vek3w.ru';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        //$this->installDB();
            global $DB;
            $site_format = CSite::GetDateFormat("FULL");
            // переведем формат сайта в формат PHP
            $php_format = $DB->DateFormatToPHP($site_format);
            $DateTo = date($php_format, time());
                CAgent::AddAgent(
                                    "Task_user::Agent_start_task();", // имя функции
                                    "garp_task_user",                          // идентификатор модуля
                                    "N",                                  // агент не критичен к кол-ву запусков
                                    86400,                                // интервал запуска - 1 сутки
                                    $DateTo,                // дата первой проверки на запуск
                                    "Y",                                  // агент активен
                                    $DateTo,                // дата первого запуска
                                    30
                    );

    }

    public function doUninstall()
    {
        //$this->uninstallDB();
        ModuleManager::unregisterModule($this->MODULE_ID);

    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID))
        {
            ExampleTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID))
        {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(ExampleTable::getTableName());
        }
    }
}
