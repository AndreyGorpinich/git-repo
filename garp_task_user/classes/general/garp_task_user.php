<?

/**
* 
*/
class Task_user  {
	
	function Agent_start_task(){
		$option_director = COption::GetOptionString( 'garp_task_user', 'option-director');
		$option_responsible = COption::GetOptionString( 'garp_task_user', 'option-responsible');
		$option_accomplice = COption::GetOptionString( 'garp_task_user', 'option-accomplice');
		$option_observers = COption::GetOptionString( 'garp_task_user', 'option-observers');
		$option_name = COption::GetOptionString( 'garp_task_user', 'option-name');

if (CModule::IncludeModule("tasks"))
{
$option_accomplice = explode(',', $option_accomplice);
$option_observers = explode(',', $option_observers);
	//print_r($option_accomplice);
	//print_r($option_observers);
global $DB;
global $USER;
global $APPLICATION;
// получим полный формат сайта
$site_format = CSite::GetDateFormat("FULL");
// переведем формат сайта в формат PHP
$php_format = $DB->DateFormatToPHP($site_format);
// сейчас
$today = time();
// кол-во секунд в сутках
$day = 86400; 
// кол-во секунд в  сутках
$l_13_day = $today + ($day*13);
$l_15_day = $today + ($day*15);
// дата, которая была 30 дней назад
$DateFrom = date($php_format, $l_13_day);
$DateFt = date($php_format, $l_15_day);
// текущая дата в формате текущего сайта
$DateTo = date($php_format, $today);

$deadline = $DateFrom;
//echo $DateFrom;
//echo " /////";
//echo $DateFt ;

$filter = Array
(

   "<UF_CHECK_DAY"           => $DateFt ,
   ">UF_CHECK_DAY"           => $DateFrom,
  
);

$rsUsers = CUser::GetList(($by="LAST_NAME"), ($order="asc"),  $filter);  //, array( array("UF_CHECK_DAY") , array("nPageSize"=>"1"))
if($arUser = $rsUsers->Fetch()) :


    $arFields = Array(
        "TITLE" => $option_name ,
        "RESPONSIBLE_ID" => $option_responsible,
        "DEADLINE" => $deadline,
        "CREATED_BY" => $option_director,
        "AUDITORS" => $option_observers,
        "ACCOMPLICES" => $option_accomplice
    );

    $obTask = new CTasks;
    $ID = $obTask->Add($arFields);
    $success = ($ID>0);

    if($success)
    {
        return 'Task_user::Agent_start_task();';
    }
    else
    {
        if($e = $APPLICATION->GetException()){
            return "Error: ".$e->GetString();  
        }

    }

endif;
/**/
}




		
		return 'Task_user::Agent_start_task();';

	
	}
}


?>