<?
/**
 * Идентификатор модуля
 */
$sModuleId  = 'garp_task_user';
 
/**
 * Подключаем модуль (выполняем код в файле include.php)
 */
CModule::IncludeModule( $sModuleId );
 
/**
 * Языковые константы (файл lang/ru/options.php)
 */
global $MESS;
IncludeModuleLangFile( __FILE__ );
 
if( $REQUEST_METHOD == 'POST' && $_POST['Update'] == 'Y' ) {
    /**
     * Если форма была сохранена, устанавливаем значение опции модуля
     */
    COption::SetOptionString( $sModuleId, 'option-director', $_POST['option-director']);
    COption::SetOptionString( $sModuleId, 'option-responsible', $_POST['option-responsible']);
    COption::SetOptionString( $sModuleId, 'option-accomplice', implode(',',$_POST['option-accomplice']));
    COption::SetOptionString( $sModuleId, 'option-observers', implode(',',$_POST['option-observers']));
    COption::SetOptionString( $sModuleId, 'option-name', $_POST['option-name']);
 //print_r($_POST['option-accomplice']);
}

/**
 * Описываем табы административной панели битрикса
 */
$aTabs = array(
    array(
        'DIV'   => 'edit1',
        'TAB'   => GetMessage('MAIN_TAB_SET'),
        'ICON'  => 'fileman_settings',
        'TITLE' => GetMessage('MAIN_TAB_TITLE_SET' )
    ),
);

$filter = Array
(
    "!LAST_NAME"           => "",
    "!NAME"           => ""
);
$rsUsers = CUser::GetList(($by="last_name"), ($order="asc"), $filter); 
while($rsUsers->NavNext(true, "f_")) :
    $user_list[$f_ID]['LAST_NAME']=$f_LAST_NAME;
    $user_list[$f_ID]['NAME']=$f_NAME;
    $user_list[$f_ID]['ID']=$f_ID;
endwhile;
 
/**
 * Инициализируем табы
 */
$oTabControl = new CAdmintabControl( 'tabControl', $aTabs );
$oTabControl->Begin();
 


/**
 * Ниже пошла форма страницы с настройками модуля
 */
?><form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars( $sModuleId )?>&lang=<?echo LANG?>">
    <?=bitrix_sessid_post()?>
    <?$oTabControl->BeginNextTab();?>
    <tr class="heading">
        <td colspan="2"><?=GetMessage( 'DD_BM_GROUP_TITLE' )?></td>
    </tr>
        <tr>
        <td width="50%" valign="top"><label for="option-name"><?echo GetMessage( 'option-name' );?></label>:</td>
        <td  valign="top">
                <input name="option-name" id="option-name" value="<?=COption::GetOptionString( $sModuleId, 'option-name')?>">
          
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option-director"><?echo GetMessage( 'option-director' );?></label>:</td>
        <td  valign="top">
                <select  name="option-director" id="option-director" >
                <?foreach($user_list as $user_o) :?>
                    <? if ( COption::GetOptionString( $sModuleId, 'option-director') == $user_o['ID']): $selected="selected"; else : $selected=""; endif; ?>
                    <option <?=$selected?> value="<?=$user_o['ID']?>"><?=$user_o['LAST_NAME']?> <?=$user_o['NAME']?></option>
                <?endforeach;?>
                </select>
      
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option"><?echo GetMessage( 'option-responsible' );?></label>:</td>
        <td  valign="top">
            <select  name="option-responsible" id="option-responsible" >
                <?foreach($user_list as $user_o) :?>
                    <? if ( COption::GetOptionString( $sModuleId, 'option-responsible') == $user_o['ID']): $selected="selected"; else : $selected=""; endif; ?>
                    <option <?=$selected?> value="<?=$user_o['ID']?>"><?=$user_o['LAST_NAME']?> <?=$user_o['NAME']?></option>
                <?endforeach;?>
            </select>

        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option"><?echo GetMessage( 'option-accomplice' );?></label>:</td>
        <td  valign="top">
              <select multiple size="10"   name="option-accomplice[]" id="option-accomplice" >
                <?foreach($user_list as $user_o) :?>
                    <?if ( in_array($user_o['ID'], explode(',',COption::GetOptionString( $sModuleId, 'option-accomplice')))): $selected="selected"; else : $selected=""; endif; ?>
                    <option <?=$selected?> value="<?=$user_o['ID']?>"><?=$user_o['LAST_NAME']?> <?=$user_o['NAME']?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option"><?echo GetMessage( 'option-observers' );?></label>:</td>
        <td  valign="top">
            <select multiple size="10" name="option-observers[]" id="option-observers" >
                <?foreach($user_list as $user_o) :?>
                    <? if ( in_array($user_o['ID'], explode(',',COption::GetOptionString( $sModuleId, 'option-observers')))): $selected="selected"; else : $selected=""; endif; ?>
                    <option <?=$selected?> value="<?=$user_o['ID']?>"><?=$user_o['LAST_NAME']?> <?=$user_o['NAME']?></option>
                <?endforeach;?>
            </select> 
        </td>
    </tr>
    <?$oTabControl->Buttons();?>
    <input type="submit" name="Update" value="<?=GetMessage( 'DD_BM_BUTTON_SAVE' )?>" />
    <input type="reset" name="reset" value="<?= GetMessage( 'DD_BM_BUTTON_RESET' )?>" />
    <input type="hidden" name="Update" value="Y" />
    <?$oTabControl->End();?>
</form>