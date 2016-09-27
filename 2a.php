<?
//$_SERVER["DOCUMENT_ROOT"] = "путь для крон";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);


require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php"); 
$APPLICATION->SetTitle("Title");
CModule::IncludeModule("sale");
CModule::IncludeModule("block");
?>
<?



// получим полный формат сайта
$site_format = CSite::GetDateFormat("FULL");

// переведем формат сайта в формат PHP
$php_format = $DB->DateFormatToPHP($site_format);

// сейчас
$today = time();
// кол-во секунд в сутках
$day = 86400; 
// кол-во секунд в 30 сутках
$last_30_day = $today - ($day*30);

// дата, которая была 30 дней назад
echo $DateFrom = date($php_format, $last_30_day);


// текущая дата в формате текущего сайта
$DateTo = date($php_format, $today);



$arBasketItems = array();

	$dbBasketItems = CSaleBasket::GetList(
				array(
						"PRICE" => "ASC"
					),
				array(
						"LID" => SITE_ID,
						"DELAY"=>"Y", //Список отложенных товаров
						">DATE_INSERT"=> $DateFrom , //Добавлены за последнии 30 дней
						"ORDER_ID" => "NULL"
					),
				false,
				false,
				array("ID",  "PRODUCT_ID","DELAY","USER_ID", "CAN_BUY","ORDER_ID" )
	);
		while ($arItems = $dbBasketItems->Fetch())
		{

			$rsOrder = CSaleOrder::GetList(
					array(), 
					array(
					">DATE_INSERT"=> $DateFrom , //Добавлены за последнии 30 дней
					"USER_ID" => $arItems["USER_ID"], //ID пользователя
					'BASKET_PRODUCT_ID' => $arItems['PRODUCT_ID'] //ID Товара
					)
			);

			$arOR = $rsOrder->Fetch();

				if(!$arOR['ID']){ // Если нет в закзах пользователя за последнии 30 дней 

					$rsUser = CUser::GetByID($arItems["USER_ID"]);
						$arUser = $rsUser->Fetch();

							$resp = CIBlockElement::GetByID($arItems['PRODUCT_ID']);
							$ar_resp = $resp->GetNext();
							$arBasketItems[$arItems["USER_ID"]]['ITEMS'][] =  $ar_resp['NAME']; //Собираем массив для каждого пользователя
							$arBasketItems[$arItems["USER_ID"]]['EMAIL'] = $arUser['EMAIL'];
							$arBasketItems[$arItems["USER_ID"]]['NAME'] =  $arUser['NAME'];
							$arBasketItems[$arItems["USER_ID"]]['LAST_NAME'] =  $arUser['LAST_NAME'];
				}

		}

foreach($arBasketItems as $form_item){

		$list_item = implode("<br>", $form_item['ITEMS']);

		$arEventFields = array(
								"EMAIL_TO"=>'garp666@yandex.ru', //$form_item['EMAIL'],
								"Имя_Фамилия"=>$form_item['NAME'].' '.$form_item['LAST_NAME'],
								"список_товаров"=>'<br>'.$list_item
								);

		CEvent::Send("DELAY_PRODUCT_TO_MAIL", 's1', $arEventFields);

}


echo "<pre>";
print_r($arEventFields);
echo "</pre>";

?>
