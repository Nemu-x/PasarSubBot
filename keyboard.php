<?php
require_once 'config.php';
$setting = select("setting", "*", null, null,"select");
$textbotlang = languagechange(__DIR__.'/text.json');
if (!function_exists('getPaySettingValue')) {
    function getPaySettingValue($name)
    {
        $result = select("PaySetting", "ValuePay", "NamePay", $name, "select");
        return $result['ValuePay'] ?? null;
    }
}
//-----------------------------[  text panel  ]-------------------------------
$stmt = $pdo->prepare("SHOW TABLES LIKE 'textbot'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
$datatextbot = array(
    'text_usertest' => '',
    'text_Purchased_services' => '',
    'text_support' => '',
    'text_help' => '',
    'text_start' => '',
    'text_bot_off' => '',
    'text_dec_info' => '',
    'text_dec_usertest' => '',
    'text_fq' => '',
    'accountwallet' => '',
    'text_sell' => '',
    'text_Add_Balance' => '',
    'text_Discount' => '',
    'text_Tariff_list' => '',
    'text_affiliates' => '',
    'carttocart' => '',
    'textnowpayment' => '',
    'textnowpaymenttron' => '',
    'iranpay1' => '',
    'iranpay2' => '',
    'iranpay3' => '',
    'aqayepardakht' => '',
    'zarinpal' => '',
    'text_fq' => '',
    'textpaymentnotverify' =>"",
    'textrequestagent' => '',
    'textpanelagent' => '',
    'text_wheel_luck' => '',
    'text_star_telegram' => "",
    'text_extend' => '',
    'textsnowpayment' => ''

);
if ($table_exists) {
    $textdatabot =  select("textbot", "*", null, null,"fetchAll");
    $data_text_bot = array();
    foreach ($textdatabot as $row) {
        $data_text_bot[] = array(
            'id_text' => $row['id_text'],
            'text' => $row['text']
        );
    }
    foreach ($data_text_bot as $item) {
        if (isset($datatextbot[$item['id_text']])) {
            $datatextbot[$item['id_text']] = $item['text'];
        }
    }
}
$datatextbot = localizeTextbotLabels($datatextbot, $textbotlang);
$adminrulecheck = select("admin", "*", "id_admin", $from_id,"select");
if (!$adminrulecheck) {
    $adminrulecheck = array(
        'rule' => '',
    );
}
$users = select("user", "*", "id", $from_id,"select");
if ($users == false) {
    $users = array();
    $users = array(
        'step' => '',
        'agent' => '',
        'limit_usertest' => '',
        'Processing_value' => '',
        'Processing_value_four' => '',
        'cardpayment' => ""
    );
}
$replacements = [
    'text_usertest' => $datatextbot['text_usertest'],
    'text_Purchased_services' => $datatextbot['text_Purchased_services'],
    'text_support' => $datatextbot['text_support'],
    'text_help' => $datatextbot['text_help'],
    'accountwallet' => $datatextbot['accountwallet'],
    'text_sell' => $datatextbot['text_sell'],
    'text_Tariff_list' => $datatextbot['text_Tariff_list'],
    'text_affiliates' => $datatextbot['text_affiliates'],
    'text_wheel_luck' => $datatextbot['text_wheel_luck'],
    'text_extend' => $datatextbot['text_extend']
];
$admin_idss = select("admin", "*", "id_admin", $from_id,"count");
$temp_addtional_key = [];
$keyboardLayout = json_decode($setting['keyboardmain'], true);
$keyboardRows = [];
if (is_array($keyboardLayout) && isset($keyboardLayout['keyboard']) && is_array($keyboardLayout['keyboard'])) {
    $keyboardRows = $keyboardLayout['keyboard'];
}

if ($setting['inlinebtnmain'] == "oninline" && !empty($keyboardRows)) {
    $trace_keyboard = $keyboardRows;
    foreach ($trace_keyboard as $key => $callback_set) {
        foreach ($callback_set as $keyboard_key => $keyboard) {
            if ($keyboard['text'] == "text_sell") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "buy";
            }
            if ($keyboard['text'] == "accountwallet") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "account";
            }
            if ($keyboard['text'] == "text_Tariff_list") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "Tariff_list";
            }
            if ($keyboard['text'] == "text_wheel_luck") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "wheel_luck";
            }
            if ($keyboard['text'] == "text_affiliates") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "affiliatesbtn";
            }
            if ($keyboard['text'] == "text_extend") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "extendbtn";
            }
            if ($keyboard['text'] == "text_support") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "supportbtns";
            }
            if ($keyboard['text'] == "text_Purchased_services") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "backorder";
            }
            if ($keyboard['text'] == "text_help") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "helpbtns";
            }
            if ($keyboard['text'] == "text_usertest") {
                $trace_keyboard[$key][$keyboard_key]['callback_data'] = "usertestbtn";
            }
        }
    }
    if ($admin_idss != 0) {
        $temp_addtional_key[] = ['text' => $textbotlang['Admin']['textpaneladmin'], 'callback_data' => "admin"];
    }
    if ($users['agent'] != "f") {
        $temp_addtional_key[] = ['text' => $datatextbot['textpanelagent'], 'callback_data' => "agentpanel"];
    }
    if ($users['agent'] == "f" && $setting['statusagentrequest'] == "onrequestagent") {
        $temp_addtional_key[] = ['text' => $datatextbot['textrequestagent'], 'callback_data' => "requestagent"];
    }
    $keyboard = ['inline_keyboard' => []];
    $keyboardcustom = $trace_keyboard;
    $keyboardcustom = json_decode(strtr(strval(json_encode($keyboardcustom)), $replacements), true);
    $keyboardcustom[] = $temp_addtional_key;
    $keyboard['inline_keyboard'] = $keyboardcustom;
    $keyboard = json_encode($keyboard);
} else {
    if ($admin_idss != 0) {
        $temp_addtional_key[] = ['text' => $textbotlang['Admin']['textpaneladmin']];
    }
    if ($users['agent'] != "f") {
        $temp_addtional_key[] = ['text' => $datatextbot['textpanelagent']];
    }
    if ($users['agent'] == "f" && $setting['statusagentrequest'] == "onrequestagent") {
        $temp_addtional_key[] = ['text' => $datatextbot['textrequestagent']];
    }
    $keyboard = ['keyboard' => [], 'resize_keyboard' => true];
    $keyboardcustom = $keyboardRows;
    $keyboardcustom = json_decode(strtr(strval(json_encode($keyboardcustom)), $replacements), true);
    $keyboardcustom[] = $temp_addtional_key;
    $keyboard['keyboard'] = $keyboardcustom;
    $keyboard = json_encode($keyboard);
}

$keyboardPanel = json_encode([
    'inline_keyboard' => [
        [['text' => $datatextbot['text_Discount'] ,'callback_data' => "Discount"],
        ['text' => $datatextbot['text_Add_Balance'] ,'callback_data' => "Add_Balance"]
        ],
        [['text' => $textbotlang['users']['backbtn'] ,'callback_data' => "backuser"]],
    ],
    'resize_keyboard' => true
]);
if($adminrulecheck['rule'] == "administrator"){
$kl = $textbotlang['Admin']['keyboard_labels'];
$keyboardadmin = json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['Status']['btn']]],
        [['text' => $textbotlang['Admin']['btnkeyboardadmin']['managementpanel']],['text' => $textbotlang['Admin']['btnkeyboardadmin']['addpanel']]],
        [['text' => $kl['quick_price_time']],['text' => $kl['quick_price_volume']]],
        [['text' => $textbotlang['Admin']['btnkeyboardadmin']['managruser']],['text' => $kl['shop_settings']]],
        [['text' => $kl['finance']]],
        [['text' => $kl['support_section']],['text' => $kl['tutorial_section']]],
        [['text' => $kl['report_bug']],['text' => $kl['panel_features']]],
        [['text' => $kl['general_settings']],['text' => $kl['unverified_payments']]],
        [['text' => $textbotlang['users']['backbtn']]]
    ],
    'resize_keyboard' => true
]);
}
if($adminrulecheck['rule'] == "Seller"){
$kl = $textbotlang['Admin']['keyboard_labels'];
$keyboardadmin = json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['Status']['btn']]],
        [['text' => $kl['manage_user']]],
        [['text' => $textbotlang['users']['backbtn']]]
    ],
    'resize_keyboard' => true
]);
}
if($adminrulecheck['rule'] == "support"){
$kl = $textbotlang['Admin']['keyboard_labels'];
$keyboardadmin = json_encode([
    'keyboard' => [
        [['text' => $kl['manage_user']],['text' => $kl['search_user']]],
        [['text' => $textbotlang['users']['backbtn']]]
    ],
    'resize_keyboard' => true
]);
}
$CartManage = json_encode([
    'keyboard' => [
        [['text' => "🗂 Card payment gateway name"]],
        [['text' => "💳 Set card number"],['text' => "❌ Delete card number"]],
        [['text' => "👤 Support ID", ],['text' => "💳 Offline gateway in PM"]],
        [['text' => "💰 Disable card display"],['text' => "💰 Enable card display"]],
        [['text' => "♻️ Group card display"]],
        [['text' => "📄 Export users with active card"]],
        [['text' => "♻️ Auto receipt approval"],['text' => "💰 Card-to-card cashback"]],
        [['text' => "🔒 Show card after first payment"]],
        [['text' => "⬇️ Min card payment amount"],['text' => "⬆️ Max card payment amount"]],
        [['text' => "📚 Set card payment guide"]],
        [['text' => "🤖 Approve receipt without review"]],
        [['text' => "💳 Exclude user from auto approval"]],
        [['text' => "⏳ Auto approval delay"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$trnado = json_encode([
    'keyboard' => [
        [['text' => "🗂 Rial/FX gateway name (2)"]],
        [['text' => "API T"]],
        [['text' => "Set API address"]],
        [['text' => "💰 Rial/FX cashback (2)"]],
        [['text' => "⬇️ Min amount Rial/FX (2)"],['text' => "⬆️ Max amount Rial/FX (2)"]],
        [['text' => "📚 Set Rial/FX guide (2)"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$keyboardzarinpal = json_encode([
    'keyboard' => [
        [['text' => "🗂 ZarinPal gateway name"],['text' => "ZarinPal merchant"]],
        [['text' => "💰 ZarinPal cashback"]],
        [['text' => "⬇️ Min amount ZarinPal"],['text' => "⬆️ Max amount ZarinPal"]],
        [['text' => "📚 Set ZarinPal guide"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$aqayepardakht = json_encode([
    'keyboard' => [
        [['text' => "🗂 AghayePardakht gateway name"]],
        [['text' => "Set AghayePardakht merchant"],['text' => "💰 AghayePardakht cashback"]],
        [['text' => "⬇️ Min amount AghayePardakht"],['text' => "⬆️ Max amount AghayePardakht"]],
        [['text' => "📚 Set AghayePardakht guide"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$NowPaymentsManage = json_encode([
    'keyboard' => [
        [['text' => "🗂 Plisio gateway name"]],
        [['text' => "🧩 Plisio API"],['text'=> "💰 Plisio cashback"]],
        [['text' => "⬇️ Min amount Plisio"],['text' =>"⬆️ Max amount Plisio"]],
        [['text' => "📚 Set Plisio guide"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$klsp = $textbotlang['Admin']['keyboard_labels'];
$setting_panel =  json_encode([
    'keyboard' => [
        [['text' => $klsp['feature_toggles']]],
        [['text' => $klsp['bot_reports']], ['text' => $klsp['channel_settings']]],
        [['text' => $klsp['enable_web_panel']]],
        [['text' => $klsp['optimize_bot']]],
        [['text' => $klsp['bot_text_settings']],['text' => $klsp['admin_users_section']]],
        [['text' => $textbotlang['Admin']['getlimitusertest']['setlimitbtn']]],
        [['text' => $klsp['agent_join_fee']],['text' => $klsp['qrcode_background']]],
        [['text' => $klsp['agent_bots_webhook']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$PaySettingcard = getPaySettingValue("Cartstatus");
$PaySettingnow = getPaySettingValue("nowpaymentstatus");
$PaySettingaqayepardakht = getPaySettingValue("statusaqayepardakht");
$PaySettingpv = getPaySettingValue("Cartstatuspv");
$usernamecart = getPaySettingValue("CartDirect");
$Swapino = getPaySettingValue("statusSwapWallet");
$trnadoo = getPaySettingValue("statustarnado");
$paymentverify = getPaySettingValue("checkpaycartfirst");
$stmt = $pdo->prepare("SELECT * FROM Payment_report WHERE id_user = '$from_id' AND payment_Status = 'paid' ");
$stmt->execute();
$paymentexits = $stmt->rowCount();
$zarinpal = getPaySettingValue("zarinpalstatus");
$affilnecurrency = getPaySettingValue("digistatus");
$arzireyali3 = getPaySettingValue("statusiranpay3");
$paymentstatussnotverify = getPaySettingValue("paymentstatussnotverify");
$paymentsstartelegram = getPaySettingValue("statusstar");
$payment_status_nowpayment = getPaySettingValue("statusnowpayment");
$step_payment = [
    'inline_keyboard' => []
    ];
   if($PaySettingcard == "oncard" && intval($users['cardpayment']) == 1){
        if($PaySettingpv == "oncardpv"){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['carttocart'] ,'url' => "https://t.me/$usernamecart"],
    ];
        }else{
                    $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['carttocart'] ,'callback_data' => "cart_to_offline"],
    ];
        }
    }
    if(($paymentexits == 0 && $paymentverify == "onpayverify"))unset($step_payment['inline_keyboard']);
   if($PaySettingnow == "onnowpayment"){
        $step_payment['inline_keyboard'][] = [
    ['text' => $datatextbot['textnowpayment'], 'callback_data' => "plisio" ]
    ];
    }
    if($payment_status_nowpayment == "1"){
        $step_payment['inline_keyboard'][] = [
    ['text' => $datatextbot['textsnowpayment'], 'callback_data' => "nowpayment" ]
    ];
    }
   if($affilnecurrency == "ondigi"){
        $step_payment['inline_keyboard'][] = [
            ['text' =>  $datatextbot['textnowpaymenttron'], 'callback_data' => "digitaltron" ]
    ];
    }
   if($Swapino == "onSwapinoBot"){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['iranpay2'] , 'callback_data' => "iranpay1" ]
    ];
    }
   if($trnadoo == "onternado"){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['iranpay3'] , 'callback_data' => "iranpay2" ]
    ];
    }
     if($arzireyali3 == "oniranpay3"  && $paymentexits >= 2){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['iranpay1'] , 'callback_data' => "iranpay3" ]
    ];
    }
   if($PaySettingaqayepardakht == "onaqayepardakht"){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['aqayepardakht'] , 'callback_data' => "aqayepardakht" ]
    ];
    }
    if($zarinpal == "onzarinpal"){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['zarinpal'] , 'callback_data' => "zarinpal" ]
    ];
    }
    if($paymentstatussnotverify == "onverifypay"){
        $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['textpaymentnotverify'] , 'callback_data' => "paymentnotverify" ]
    ];
    }
    if(intval($paymentsstartelegram) == 1){
     $step_payment['inline_keyboard'][] = [
            ['text' => $datatextbot['text_star_telegram'] , 'callback_data' => "startelegrams" ]
    ];   
    }
    $step_payment['inline_keyboard'][] = [
            ['text' => "❌ Close list" , 'callback_data' => "colselist" ]
    ];
    $step_payment = json_encode($step_payment);
$keyboardhelpadmin = json_encode([
    'keyboard' => [
        [['text' => "📚 Add tutorial"], ['text' => "❌ Delete tutorial"]],
        [['text' => "✏️ Edit tutorial"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$shopkeyboard = json_encode([
    'keyboard' => [
        [['text' => "🛒 Shop feature status"]],
        [['text' => "🗂 Category management"],['text' => "🛍 Product management"]],
        [['text' => "🎁 Create gift code"],['text' => "❌ Delete gift code"]],
        [['text' => "🎁 Create discount code"],['text' => "❌ Delete discount code"]],
        [['text' => "⬇️ Min balance for bulk buy"],['text' => "🎁 Renewal cashback"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$keyboard_Category_manage = json_encode([
    'keyboard' => [
        [['text' => "🛒 Add category"],['text' => "❌ Delete category"]],
        [['text' => "✏️ Edit category"]],
        [['text' => "⬅️ Back to shop menu"]]
    ],
    'resize_keyboard' => true
    ]);
$keyboard_shop_manage = json_encode([
    'keyboard' => [
        [['text' => "🛍 Add product"], ['text' => "❌ Delete product"]],
        [['text' => "✏️ Edit product"]],
        [['text' => "⬆️ Increase prices in bulk"],['text' => "⬇️ Decrease prices in bulk"]],
        [['text' => "⬅️ Back to shop menu"]]
    ],
    'resize_keyboard' => true
]);
if($setting['inlinebtnmain'] == "oninline"){
    $confrimrolls = json_encode([
    'inline_keyboard' => [
        [
            ['text' => "✅ I accept the rules", 'callback_data' => "acceptrule"],
            ],
    ]
    ]);
}else{
$confrimrolls = json_encode([
    'keyboard' => [
        [['text' => "✅ I accept the rules"]],
    ],
    'resize_keyboard' => true
]);
}
$request_contact = json_encode([
    'keyboard' => [
        [['text' => "☎️ Send phone number", 'request_contact' => true]],
        [['text' => $textbotlang['users']['backbtn']]]
    ],
    'resize_keyboard' => true
]);
$Feature_status = json_encode([
    'keyboard' => [
        [['text' => "Account info visibility"]],
        [['text' => "Test account feature"], ['text' => "Education feature"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$channelkeyboard = json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['channel']['title']],['text' => $textbotlang['Admin']['channel']['removechannelbtn']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
if($setting['inlinebtnmain'] == "oninline"){
    $backuser = json_encode([
        'inline_keyboard' => [
        [['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"]]
    ],
]);
}else{
$backuser = json_encode([
        'keyboard' => [
        [['text' => $textbotlang['users']['backbtn']]]
    ],
    'resize_keyboard' => true,
    'input_field_placeholder' =>"برای بازگشت روی دکمه زیر کلیک کنید"
]);
}
$backadmin = json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true,
    'input_field_placeholder' =>"برای بازگشت روی دکمه زیر کلیک کنید"
]);
//------------------  [ list panel ]----------------//
$stmt = $pdo->prepare("SHOW TABLES LIKE 'marzban_panel'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
$namepanel = [];
if ($table_exists) {
    $stmt = $pdo->prepare("SELECT * FROM marzban_panel");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $namepanel[] = [$row['name_panel']];
    }
    $list_marzban_panel = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    foreach ($namepanel as $button) {
        $list_marzban_panel['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
        $list_marzban_panel['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
        ['text' => $textbotlang['Admin']['backmenu']]
    ];
    $json_list_marzban_panel = json_encode($list_marzban_panel);
//------------------  [ list panel inline ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM marzban_panel");
    $stmt->execute();
    $list_marzban_panel_edit_product = ['inline_keyboard' => []];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list_marzban_panel_edit_product['inline_keyboard'][] = [['text' =>$row['name_panel'],'callback_data' => 'locationedit_'.$row['code_panel']]];
    }
    $list_marzban_panel_edit_product['inline_keyboard'][] = [['text' =>"All panels",'callback_data' => 'locationedit_all']];
    $list_marzban_panel_edit_product['inline_keyboard'][] = [['text' =>"▶️ Back to previous menu",'callback_data' => 'backproductadmin']];
    $list_marzban_panel_edit_product = json_encode($list_marzban_panel_edit_product);
}
//------------------  [ list channel ]----------------//
$stmt = $pdo->prepare("SHOW TABLES LIKE 'channels'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
$list_channels = [];
if ($table_exists) {
    $stmt = $pdo->prepare("SELECT * FROM channels");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list_channels[] = [$row['link']];
    }
    $list_channels_join = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    foreach ($list_channels as $button) {
        $list_channels_join['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
        $list_channels_join['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
        ['text' => $textbotlang['Admin']['backmenu']]
    ];
    $list_channels_joins = json_encode($list_channels_join);
}
//------------------  [ list card ]----------------//
$stmt = $pdo->prepare("SHOW TABLES LIKE 'card_number'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
$list_card = [];
if ($table_exists) {
    $stmt = $pdo->prepare("SELECT * FROM card_number");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list_card[] = [$row['cardnumber']];
    }
    $list_card_remove = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    foreach ($list_card as $button) {
        $list_card_remove['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
        $list_card_remove['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
        ['text' => $textbotlang['Admin']['backmenu']]
    ];
    $list_card_remove = json_encode($list_card_remove);
}
//------------------  [ help list ]----------------//
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'help'");
    $stmt->execute();
    $result = $stmt->fetchAll();
    $table_exists = count($result) > 0;
    if ($table_exists) {
    $stmt = $pdo->prepare("SELECT * FROM help");
    $stmt->execute();
    $helpkey = [];
    $stmt = $pdo->prepare("SELECT * FROM help");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $helpkey[] = [$row['name_os']];
        }
        $help_arrke = [
            'keyboard' => [],
            'resize_keyboard' => true,
        ];
        foreach ($helpkey as $button) {
            $help_arrke['keyboard'][] = [
                ['text' => $button[0]]
            ];
        }
                $help_arrke['keyboard'][] = [
            ['text' => $textbotlang['users']['backbtn']],
        ];
        $json_list_helpkey = json_encode($help_arrke);
}
//------------------  [ help list ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM help");
    $stmt->execute();
    $helpcwtgory = ['inline_keyboard' => []];
    $datahelp = [];
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if(in_array($result['category'],$datahelp))continue;
        if($result['category'] == null)continue;
        $datahelp[] = $result['category'];
            $helpcwtgory['inline_keyboard'][] = [['text' => $result['category'], 'callback_data' => "helpctgoryـ{$result['category']}"]
            ];
        }
if($setting['linkappstatus'] == "1"){
    $helpcwtgory['inline_keyboard'][] = [
        ['text' => "🔗 App download link", 'callback_data' => "linkappdownlod"],
    ];    
    }
$helpcwtgory['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"],
];
$json_list_helpـcategory = json_encode($helpcwtgory);


//------------------  [ help app ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM app");
    $stmt->execute();
    $helpapp = ['inline_keyboard' => []];
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $helpapp['inline_keyboard'][] = [['text' => $result['name'], 'url' =>$result['link']]
            ];
        }
$helpapp['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"],
];
$json_list_helpـlink = json_encode($helpapp);
//------------------  [ help app admin ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM app");
    $stmt->execute();
    $helpappremove = ['keyboard' => [],'resize_keyboard' => true];
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $helpappremove['keyboard'][] = [
            ['text' => $result['name']],
        ];
        }
$helpappremove['keyboard'][] = [
    ['text' => $textbotlang['Admin']['backadmin']],
];
$json_list_remove_helpـlink = json_encode($helpappremove);
 //------------------  [ listpanelusers ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM marzban_panel WHERE status = 'active' AND (agent = :agent OR agent = 'all')");
    $stmt->bindParam(':agent', $users['agent']);
    $stmt->execute();
    $list_marzban_panel_users = ['inline_keyboard' => []];
    $panelcount = select("marzban_panel","*","status","active","count");
    if($panelcount > 10){
        $temp_row = [];
         while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($result['hide_user'] != null && in_array($from_id, json_decode($result['hide_user'], true))) continue;
        if($result['type'] == "Manualsale"){
            $stmt = $pdo->prepare("SELECT * FROM manualsell WHERE codepanel = :codepanel AND status = 'active'");
            $stmt->bindParam(':codepanel', $result['code_panel']);
            $stmt->execute();
            $configexits = $stmt->rowCount();
            if(intval($configexits) == 0)continue;
        }
        if ($users['step'] == "getusernameinfo") {
            $temp_row[] = ['text' => $result['name_panel'], 'callback_data' => "locationnotuser_{$result['code_panel']}"];
        } else {
            $temp_row[] = ['text' => $result['name_panel'], 'callback_data' => "location_{$result['code_panel']}"];
        }
         if (count($temp_row) == 2) {
            $list_marzban_panel_users['inline_keyboard'][] = $temp_row;
            $temp_row = []; 
        }
    } 
        if (!empty($temp_row)) {
        $list_marzban_panel_users['inline_keyboard'][] = $temp_row;
    }
    }else{
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($result['type'] == "Manualsale"){
            $stmts = $pdo->prepare("SELECT * FROM manualsell WHERE codepanel = :codepanel AND status = 'active'");
            $stmts->bindParam(':codepanel', $result['code_panel']);
            $stmts->execute();
            $configexits = $stmts->rowCount();
            if(intval($configexits) == 0)continue;
        }
        if($result['hide_user'] != null and in_array($from_id,json_decode($result['hide_user'],true)))continue;
        if ($users['step'] == "getusernameinfo") {
            $list_marzban_panel_users['inline_keyboard'][] = [
                ['text' => $result['name_panel'], 'callback_data' => "locationnotuser_{$result['code_panel']}"]
            ];
        }
        else{
            $list_marzban_panel_users['inline_keyboard'][] = [['text' => $result['name_panel'], 'callback_data' => "location_{$result['code_panel']}"]
            ];
        }
    }
    }
$statusnote = false; 
if($setting['statusnamecustom'] == 'onnamecustom')$statusnote = true;
if($setting['statusnoteforf'] == "0" && $users['agent'] == "f")$statusnote = false;
    if($statusnote){
$list_marzban_panel_users['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "buyback"],
];
}else{
$list_marzban_panel_users['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"],
];  
}
$list_marzban_panel_user = json_encode($list_marzban_panel_users);


//------------------  [ listpanelusers omdhe ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM marzban_panel WHERE status = 'active' AND (agent = :agent OR agent = 'all')");
    $stmt->bindParam(':agent', $users['agent']);
    $stmt->execute();
    $list_marzban_panel_users_om = ['inline_keyboard' => []];
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($result['hide_user'] != null and in_array($from_id,json_decode($result['hide_user'],true)))continue;
            $list_marzban_panel_users_om['inline_keyboard'][] = [['text' => $result['name_panel'], 'callback_data' => "locationom_{$result['code_panel']}"]
            ];
    }
$list_marzban_panel_users_om['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"],
];
$list_marzban_panel_userom = json_encode($list_marzban_panel_users_om);

//------------------  [ change location ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM marzban_panel WHERE status = 'active' AND (agent = '{$users['agent']}' OR agent = 'all') AND name_panel != '{$users['Processing_value_four']}'");
    $stmt->execute();
    $list_marzban_panel_users_change = ['inline_keyboard' => []];
    $panelcount = select("marzban_panel","*","status","active","count");
    if($panelcount > 10){
        $temp_row = [];
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($result['hide_user'] != null && in_array($from_id, json_decode($result['hide_user'], true))) continue;
    
            $temp_row[] = ['text' => $result['name_panel'], 'callback_data' => "changelocselectlo-{$result['code_panel']}"];
        if (count($temp_row) == 2) {
            $list_marzban_panel_users_change['inline_keyboard'][] = $temp_row;
            $temp_row = [];
        }
    }
if (!empty($temp_row)) {
    $list_marzban_panel_users_change['inline_keyboard'][] = $temp_row;
}
    }else{
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($result['hide_user'] != null and in_array($from_id,json_decode($result['hide_user'],true)))continue;
            $list_marzban_panel_users_change['inline_keyboard'][] = [['text' => $result['name_panel'], 'callback_data' => "changelocselectlo-{$result['code_panel']}"]
            ];
    }
    }
$list_marzban_panel_users_change['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backorder"],
];
$list_marzban_panel_userschange = json_encode($list_marzban_panel_users_change);


//------------------  [ listpanelusers test ]----------------//
    $stmt = $pdo->prepare("SELECT * FROM marzban_panel WHERE TestAccount = 'ONTestAccount' AND (agent = '{$users['agent']}' OR agent = 'all')");
    $stmt->execute();
    $list_marzban_panel_usertest = ['inline_keyboard' => []];
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($result['hide_user'] != null and in_array($from_id,json_decode($result['hide_user'],true)))continue;
            $list_marzban_panel_usertest['inline_keyboard'][] = [['text' => $result['name_panel'], 'callback_data' => "locationtest_{$result['code_panel']}"]
            ];
    }
$list_marzban_panel_usertest['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"],
];
$list_marzban_usertest = json_encode($list_marzban_panel_usertest);


$textbot = json_encode([
    'keyboard' => [
        [['text' => "Set start message"], ['text' => "Purchased service button"]],
        [['text' => "Test account button"], ['text' => "FAQ button"]],
        [['text' => "📚 Education button text"], ['text' => "☎️ Support button text"]],
        [['text' => "Top-up button"],['text' => "Referral button text"]],
        [['text' => "Buy subscription button text"], ['text' => "Tariff list button text"]],
        [['text' => "Tariff list description text"]],
        [['text' => "Wallet button text"],['text' => "Invoice text"]],
        [['text' => "📝 Forced-join description text"]],
        [['text' => "📝 FAQ description text"]],
        [['text' => "⚖️ Rules text"],['text' => "After purchase text"]],
        [['text' => "After purchase text (ibsng)"],['text' => "Extend button text"]],
        [['text' => "After test account text"],['text' =>"Test cron text"]],
        [['text' => "After manual account text"]],
        [['text' => "After WGDashboard account text"]],
        [['text' => "Location selection text"],['text' => "Gift code button text"]],
        [['text' => "Agent request text"],['text' => "Agency panel button text"]],
        [['text' => "Wheel of luck button text"],['text' => "Card-to-card text"]],
        [['text' => "Set auto card-to-card text"]],
        [['text' => "Agent request description text"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
//--------------------------------------------------
$stmt = $pdo->prepare("SHOW TABLES LIKE 'protocol'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
if ($table_exists) {
    $getdataprotocol = select("protocol","*",null,null,"fetchAll");
    $protocol = [];
    foreach($getdataprotocol as $result)
    {
        $protocol[] = [['text'=>$result['NameProtocol']]];
    }
    $protocol[] = [['text'=>$textbotlang['Admin']['backadmin']]];
    $keyboardprotocollist = json_encode(['resize_keyboard'=>true,'keyboard'=> $protocol]);
 }
//--------------------------------------------------
$stmt = $pdo->prepare("SHOW TABLES LIKE 'product'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
if ($table_exists) {
    $product = [];
    $stmt = $pdo->prepare("SELECT * FROM product WHERE Location = :text or Location = '/all' ");
    $stmt->bindParam(':text', $text  , PDO::PARAM_STR);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product[] = [$row['name_product']];
    }
    $list_product = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    $list_product['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
    ];
    foreach ($product as $button) {
        $list_product['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
    $json_list_product_list_admin = json_encode($list_product);
}
//--------------------------------------------------
$stmt = $pdo->prepare("SHOW TABLES LIKE 'Discount'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
if ($table_exists) {
    $Discount = [];
    $stmt = $pdo->prepare("SELECT * FROM Discount");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $Discount[] = [$row['code']];
    }
    $list_Discount = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    $list_Discount['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
    ];
    foreach ($Discount as $button) {
        $list_Discount['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
    $json_list_Discount_list_admin = json_encode($list_Discount);
}
//--------------------------------------------------
$stmt = $pdo->prepare("SHOW TABLES LIKE 'Inbound'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
if ($table_exists) {
    $Inboundkeyboard = [];
    $stmt = $pdo->prepare("SELECT * FROM Inbound WHERE location = :Processing_value AND protocol = :text");
    $stmt->bindParam(':text', $text  , PDO::PARAM_STR);
    $stmt->bindParam(':Processing_value', $users['Processing_value']  , PDO::PARAM_STR);
    $stmt->execute();
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $Inboundkeyboard[] = [$row['NameInbound']];
}
    
}
    $list_Inbound = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    foreach ($Inboundkeyboard as $button) {
        $list_Inbound['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
        $list_Inbound['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
    ];
    $json_list_Inbound_list_admin = json_encode($list_Inbound);
}
//--------------------------------------------------
$stmt = $pdo->prepare("SHOW TABLES LIKE 'DiscountSell'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
if ($table_exists) {
    $DiscountSell = [];
    $stmt = $pdo->prepare("SELECT * FROM DiscountSell");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $DiscountSell[] = [$row['codeDiscount']];
    }
    $list_Discountsell = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    $list_Discountsell['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
    ];
    foreach ($DiscountSell as $button) {
        $list_Discountsell['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
    $json_list_Discount_list_admin_sell = json_encode($list_Discountsell);
}
$payment = json_encode([
    'inline_keyboard' => [
        [['text' => "💰 Pay and receive service", 'callback_data' => "confirmandgetservice"]],
        [['text' => "🎁 Enter discount code", 'callback_data' => "aptdc"]],
        [['text' => $textbotlang['users']['backbtn'] ,  'callback_data' => "backuser"]]
    ]
]);
$paymentom = json_encode([
    'inline_keyboard' => [
        [['text' => "💰 Pay and receive service", 'callback_data' => "confirmandgetservice"]],
        [['text' => $textbotlang['users']['backbtn'] ,  'callback_data' => "backuser"]]
    ]
]);
$change_product = json_encode([
    'keyboard' => [
        [['text' => "Price"], ['text' => "Volume"], ['text' => "Duration"]],
        [['text' => "Product name"],['text' => "User type"]],
        [['text' => "Volume reset type"],['text' => "Note"]],
        [['text' => "Product location"],['text' => "Category"]],
        [['text' => "🎛 Set inbound"],['text' => "Show on first purchase"]],
        [['text' => "Hide panel"],['text' => "Clear all hidden panels"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);

$keyboardprotocol = json_encode([
    'keyboard' => [
        [['text' => "vless"],['text' => "vmess"],['text' => "trojan"]],
        [['text' => "shadowsocks"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$MethodUsername = json_encode([
    'keyboard' => [
        [['text' => "Username + sequential number"]],
        [['text' => "Numeric ID + random chars/numbers"]],
        [['text' => "Custom username"]],
        [['text' => "Custom username + random number"]],
        [['text' => "Custom text + random number"]],
        [['text' => "Custom text + sequential number"]],
        [['text' => "Numeric ID + sequential number"]],
        [['text' => "Agent custom text + sequential number"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$mpb = $textbotlang['Admin']['managepanel']['buttons'];
$panelUi = [
    'group_name_settings' => '🎛 Group name settings',
    'service_settings' => '⚙️ Service settings',
    'custom_volume_price' => '⚙️ Custom service volume price',
    'extra_volume_price' => '➕ Extra volume price',
    'extra_time_price' => '⏳ Extra time price',
    'custom_time_price' => '⏳ Custom time price',
    'location_change_price' => '🌍 Location change price',
    'min_custom_volume' => '📍 Minimum custom volume',
    'max_custom_volume' => '📍 Maximum custom volume',
    'min_custom_time' => '📍 Minimum custom time',
    'max_custom_time' => '📍 Maximum custom time',
    'disabled_account_inbound' => '⚙️ Disabled account inbound',
    'hide_panel_user' => '🫣 Hide panel for a user',
    'unhide_panel_user' => '❌ Remove user from hidden list',
    'add_config' => '➕ Add config',
    'delete_config' => '❌ Delete config',
    'edit_config' => '✏️ Edit config',
];
$optionMarzban = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['protocol_inbound']]],
        [['text' => $mpb['renew_method']],['text' => $mpb['username_method']]],
        [['text' => $mpb['account_limit']],['text'=> $mpb['change_agent_group']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['custom_volume_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['location_change_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['disabled_account_inbound']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionibsng = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $panelUi['group_name_settings']]],
        [['text' => $mpb['renew_method']],['text' => $mpb['username_method']]],
        [['text' => $mpb['account_limit']],['text'=> $mpb['change_agent_group']]],
        [['text' => $panelUi['custom_volume_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$option_mikrotik = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $panelUi['group_name_settings']]],
        [['text' => $mpb['renew_method']],['text' => $mpb['username_method']]],
        [['text' => $mpb['account_limit']],['text'=> $mpb['change_agent_group']]],
        [['text' => $panelUi['custom_volume_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$options_ui = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['protocol_inbound']]],
        [['text' => $mpb['renew_method']],['text' => $mpb['username_method']]],
        [['text' => $mpb['account_limit']],['text'=> $mpb['change_agent_group']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['custom_volume_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['location_change_price']],
        ],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['disabled_account_inbound']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionwg = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['set_inbound_id']]],
        [['text' => $mpb['renew_method']],['text' => $mpb['username_method']]],
        [['text' => $mpb['account_limit']],['text'=> $mpb['change_agent_group']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['custom_volume_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['location_change_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['disabled_account_inbound']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionmarzneshin = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['renew_method']],
        ],
        [['text' =>$mpb['username_method']]],
        [['text' => $panelUi['service_settings']],['text' => $mpb['account_limit']]],
        [['text'=> $mpb['change_agent_group']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['location_change_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_volume_price']]],
        [['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionManualsale = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['username_method']],
        ],
        [['text' => $mpb['account_limit']],['text'=> $mpb['change_agent_group']]],
        [['text' => $panelUi['add_config']],['text' => $panelUi['delete_config']]],
        [['text' => $panelUi['edit_config']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionX_ui_single = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['renew_method']]],
        [['text' => $mpb['set_inbound_id']]],
        [['text' =>$mpb['username_method']],['text' => $mpb['sub_link_domain']]],
        [['text' => $mpb['change_agent_group']],['text' => $mpb['account_limit']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['location_change_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_volume_price']]],
        [['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionalireza_single = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text' => $mpb['edit_password']],['text' => $mpb['edit_username']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['renew_method']]],
        [['text' => $mpb['set_inbound_id']]],
        [['text' =>$mpb['username_method']]],
        [['text' => $mpb['sub_link_domain']]],
        [['text' => $mpb['change_agent_group']],['text' => $mpb['account_limit']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['location_change_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_volume_price']]],
        [['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionhiddfy = json_encode([
    'keyboard' => [
        [['text' => $mpb['features_status']]],
        [['text' => $mpb['panel_name']],['text' => $mpb['delete_panel']]],
        [['text'=> $mpb['edit_panel_url']],['text' => $mpb['renew_method']]],
        [['text' => $mpb['change_agent_group']]],
        [['text' =>$mpb['username_method']]],
        [['text' => $mpb['sub_link_domain']]],
        [['text' => $mpb['account_limit']],['text' => $mpb['admin_uuid']]],
        [['text' => $mpb['test_time']], ['text' => $mpb['test_volume']]],
        [['text' => $panelUi['location_change_price']],['text' => $panelUi['extra_volume_price']]],
        [['text' => $panelUi['extra_time_price']],['text' => $panelUi['custom_volume_price']]],
        [['text' => $panelUi['custom_time_price']]],
        [['text' => $panelUi['min_custom_volume']],['text' => $panelUi['max_custom_volume']]],
        [['text' => $panelUi['min_custom_time']],['text' => $panelUi['max_custom_time']]],
        [['text' => $panelUi['hide_panel_user']]],
        [['text' => $panelUi['unhide_panel_user']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
if($setting['statussupportpv'] == "onpvsupport"){
    $supportoption = json_encode([
        'inline_keyboard' => [
            [
                ['text' => $datatextbot['text_fq'], 'callback_data' => "fqQuestions"] ,
                ['text' => "🎟 Message support", 'url' => "https://t.me/{$setting['id_support']}"    ],
            ],[
                ['text' => "🔙 Back to main menu" ,'callback_data' => "backuser"]
            ],
 
        ]
    ]);
}else{
$supportoption = json_encode([
        'inline_keyboard' => [
            [
                ['text' => $datatextbot['text_fq'], 'callback_data' => "fqQuestions"] ,
                ['text' => "🎟 Message support", 'callback_data' => "support"],
            ],[
                ['text' => "🔙 Back to main menu" ,'callback_data' => "backuser"]
            ],
 
        ]
    ]);
}
$adminrule = json_encode([
    'keyboard' => [
        [['text' => "administrator"],['text' => "Seller"],['text' => "support"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$affiliates =  json_encode([
    'keyboard' => [
        [['text' => "🧮 Set referral percent"]],
        [['text' => "🏞 Set referral banner"]],
        [['text' => "🎁 Post-purchase commission"],['text' => "🎁 Start gift"]],
        [['text' => "🎉 Commission only on first purchase"]],
        [['text' => "🌟 Start gift amount"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$keyboardexportdata =  json_encode([
    'keyboard' => [
        [['text' => "Export users"],['text' => "Export orders"]],
        [['text' => "Export payments"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$helpedit =  json_encode([
    'keyboard' => [
        [['text' =>"Edit name"],['text' =>"Edit description"]],
        [['text' => "Edit media"],['text' => "Edit category"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$Methodextend = json_encode([
    'keyboard' => [
        [['text' => "Reset volume and time"]],
        [['text' => "Carry time and volume to next month"]],
        [['text'=> "Reset time and add previous volume"]],
        [['text' => "Reset volume and add time"]],
        [['text' => "Add time and convert total volume to remaining volume"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$keyboardtimereset = json_encode([
    'keyboard' => [
        [['text' => "no_reset"],['text' => "day"],['text' => "week"]],
        [['text' => "month"],['text' => "year"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$keyboardtypepanel = json_encode([
    'inline_keyboard' => [
        [
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_marzban'] , 'callback_data' => "typepanel#marzban"],
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_pasarguard'] , 'callback_data' => "typepanel#pasarguard"]
        ],
        [
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_marzneshin'] , 'callback_data' => "typepanel#marzneshin"]
        ],
        [
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_xui_single'], 'callback_data' => 'typepanel#x-ui_single'],
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_alireza_single'] , 'callback_data' => 'typepanel#alireza_single']
        ],
        [
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_manualsale'] , 'callback_data' => 'typepanel#Manualsale'],
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_hiddify'] , 'callback_data' => 'typepanel#hiddify'],
        ],
        [
            ['text' => "WGDashboard", 'callback_data' => 'typepanel#WGDashboard'],
            ['text' => "s_ui", 'callback_data' => 'typepanel#s_ui']
        ],
        [
            ['text' => "ibsng", 'callback_data' => 'typepanel#ibsng'],
            ['text' => $textbotlang['Admin']['managepanel']['Inbound']['type_mikrotik'], 'callback_data' => 'typepanel#mikrotik']
        ],
        [
            ['text' => $textbotlang['Admin']['backadmin'] , 'callback_data' => 'admin']
        ]
    ],
]);

$panelechekc = select("marzban_panel","*","MethodUsername","متن دلخواه نماینده + عدد ترتیبی","count");
if($setting['inlinebtnmain'] == "oninline"){
    $keyboardagent = [
    'inline_keyboard' => [
        [
            ['text' => "🗂 Bulk purchase", 'callback_data' => "kharidanbuh"],
            ['text' => "👤 Choose custom name", 'callback_data' => "selectname"]
        ],
        [
            ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"]
        ]
    ],
    'resize_keyboard' => true
];
if($panelechekc == 0){
    unset($keyboardagent['inline_keyboard'][0][1]);
}
}else{
$keyboardagent = [
    'keyboard' => [
        [['text' => "🗂 Bulk purchase"],['text' => "👤 Choose custom name"]],
        [['text' => $textbotlang['users']['backbtn']]]
    ],
    'resize_keyboard' => true
];
if($panelechekc == 0){
    unset($keyboardagent['keyboard'][0][1]);
}
}
$keyboardagent = json_encode($keyboardagent);
$Swapinokey = json_encode([
    'keyboard' => [
        [['text' => "Set API"]],
        [['text' => "🗂 Rial/FX gateway name"]],
        [['text' => "💰 Rial/FX cashback"],['text' => "📚 Set Rial/FX guide (1)"]],
        [['text' => "⬇️ Min amount Rial/FX"],['text' => "⬆️ Max amount Rial/FX"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);

$tronnowpayments = json_encode([
    'keyboard' => [
        [['text' => "🗂 Offline crypto gateway name"]],
        [['text' => "⬇️ Min amount offline crypto"],['text' => "⬆️ Max amount offline crypto"]],
        [['text' => "📚 Set offline crypto guide"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionathmarzban = json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['keyboard_labels']['manual_config_builder']],['text' => $textbotlang['Admin']['keyboard_labels']['node_management']],
        ],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$optionathx_ui = json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['keyboard_labels']['manual_config_builder']]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$configedit = json_encode([
    'keyboard' => [
        [['text' => "Config details"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$iranpaykeyboard = json_encode([
    'keyboard' => [
        [['text' => "Rial/FX gateway API (3)"]],
        [['text' => "🗂 Rial/FX gateway name (3)"]],
        [['text' => "⬇️ Min amount Rial/FX (3)"],['text' => "⬆️ Max amount Rial/FX (3)"]],
        [['text' => "💰 Rial/FX cashback (3)"]],
        [['text' => "📚 Set Rial/FX guide (3)"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$supportcenter = json_encode([
    'keyboard' => [
        [['text' => "👤 Set support ID"]],
        [['text' => "🔼 Add department"],['text' => "🔽 Remove department"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
//------------------  [ list departeman ]----------------//
$stmt = $pdo->prepare("SHOW TABLES LIKE 'departman'");
$stmt->execute();
$result = $stmt->fetchAll();
$table_exists = count($result) > 0;
$departeman = [];
if ($table_exists) {
    $stmt = $pdo->prepare("SELECT * FROM departman");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $departeman[] = [$row['name_departman']];
    }
    $departemans = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    foreach ($departeman as $button) {
        $departemans['keyboard'][] = [
            ['text' => $button[0]]
        ];
    }
        $departemans['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
        ['text' => $textbotlang['Admin']['backmenu']]
    ];
    $departemanslist = json_encode($departemans);
}
// list departeman
    $list_departman = ['inline_keyboard' => []];
 $stmt = $pdo->prepare("SELECT * FROM departman");
 $stmt->execute();
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list_departman['inline_keyboard'][] = [['text' => $result['name_departman'], 'callback_data' => "departman_{$result['id']}"]
            ];
    }
$list_departman['inline_keyboard'][] = [
    ['text' => $textbotlang['users']['backbtn'], 'callback_data' => "backuser"],
];
$list_departman = json_encode($list_departman);
$active_panell =  json_encode([
    'keyboard' => [
        [['text' => $textbotlang['Admin']['keyboard_labels']['bot_reports']]],
    ],
    'resize_keyboard' => true
]);
$lottery =  json_encode([
    'keyboard' => [
        [['text' => "1️⃣ Set first place prize"],['text' => "2️⃣ Set second place prize"]],
        [['text' => "3️⃣ Set third place prize"]],
        [['text' => $textbotlang['Admin']['backadmin']]]
    ],
    'resize_keyboard' => true
]);
$wheelkeyboard =  json_encode([
    'keyboard' => [
        [['text' => "🎲 User win amount"]],
        [['text' => $textbotlang['Admin']['backadmin']]]
    ],
    'resize_keyboard' => true
]);
$keyboardlinkapp = json_encode([
    'keyboard' => [
        [['text' => "🔗 Add app"],['text' => "❌ Delete app"]],
        [['text' => "✏️ Edit app"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
function KeyboardProduct($location,$query,$pricediscount,$datakeyboard,$statuscustom = false,$backuser = "backuser", $valuetow = null,$customvolume = "customsellvolume"){
    global $pdo,$textbotlang,$from_id;
    $product = ['inline_keyboard' => []];
    $statusshowprice = select("shopSetting","*","Namevalue","statusshowprice","select")['value'];
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    if($valuetow != null){
            $valuetow = "-$valuetow";
    }else{
            $valuetow = "";
        }
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hide_panel = json_decode($result['hide_panel'],true);
        if(in_array($location,$hide_panel))continue;
        $stmts2 = $pdo->prepare("SELECT * FROM invoice WHERE Status != 'Unpaid' AND id_user = '$from_id'");
        $stmts2->execute();
        $countorder = $stmts2->rowCount();
        if($result['one_buy_status'] == "1" && $countorder != 0 )continue;
        if(intval($pricediscount) != 0){
            $resultper = ($result['price_product'] * $pricediscount) / 100;
            $result['price_product'] = $result['price_product'] -$resultper;
        }
        $namekeyboard = $result['name_product']." - ".number_format($result['price_product']) ."تومان";
        if($statusshowprice == "onshowprice"){
            $result['name_product'] = $namekeyboard;
        }
        $product['inline_keyboard'][] = [
                ['text' =>  $result['name_product'], 'callback_data' => "{$datakeyboard}{$result['code_product']}{$valuetow}"]
            ];
    }
    if ($statuscustom)$product['inline_keyboard'][] = [['text' => $textbotlang['users']['customsellvolume']['title'], 'callback_data' => $customvolume]];
    $product['inline_keyboard'][] = [
        ['text' => $textbotlang['users']['stateus']['backinfo'], 'callback_data' => $backuser],
    ];
    return json_encode($product);
}
function KeyboardCategory($location,$agent,$backuser = "backuser"){
    global $pdo,$textbotlang;
    $stmt = $pdo->prepare("SELECT * FROM category");
    $stmt->execute();
    $list_category = ['inline_keyboard' => [],];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stmts = $pdo->prepare("SELECT * FROM product WHERE (Location = :location OR Location = '/all') AND category = :category AND agent = :agent");
        $stmts->bindParam(':location', $location, PDO::PARAM_STR);
        $stmts->bindParam(':category', $row['remark'], PDO::PARAM_STR);
        $stmts->bindParam(':agent', $agent);
        $stmts->execute();
        if($stmts->rowCount() == 0)continue;
        $list_category['inline_keyboard'][] = [['text' =>$row['remark'],'callback_data' => "categorynames_".$row['id']]];
    }
    $list_category['inline_keyboard'][] = [
        ['text' => "▶️ Back to previous menu","callback_data" => $backuser],
    ];
    return json_encode($list_category);
}

function keyboardTimeCategory($name_panel,$agent,$callback_data = "producttime_",$callback_data_back = "backuser",$statuscustomvolume = false,$statusbtnextend = false){
    global $pdo,$textbotlang;
    $stmt = $pdo->prepare("SELECT (Service_time) FROM product WHERE (Location = '$name_panel' OR Location = '/all') AND  agent = '$agent'");
    $stmt->execute();
    $montheproduct = array_flip(array_flip($stmt->fetchAll(PDO::FETCH_COLUMN)));
    $monthkeyboard = ['inline_keyboard' => []];
    if (in_array("1",$montheproduct)){
        $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['1day'], 'callback_data' => "{$callback_data}1"]
                ];
            }
    if (in_array("7",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['7day'], 'callback_data' => "{$callback_data}7"]
                ];
            }
    if (in_array("31",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['1'], 'callback_data' => "{$callback_data}31"]
                ];
            }
    if (in_array("30",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['1'], 'callback_data' => "{$callback_data}30"]
                ];
            }
    if (in_array("61",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['2'], 'callback_data' => "{$callback_data}61"]
                ];
            }
    if (in_array("60",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['2'], 'callback_data' => "{$callback_data}60"]
                ];
            }
    if (in_array("91",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['3'], 'callback_data' => "{$callback_data}91"]
                ];
            }
    if (in_array("90",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['3'], 'callback_data' => "{$callback_data}90"]
                ];
            }
    if (in_array("121",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['4'], 'callback_data' => "{$callback_data}121"]
                ];
            }
    if (in_array("120",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['4'], 'callback_data' => "{$callback_data}120"]
                ];
            }
    if (in_array("181",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['6'], 'callback_data' => "{$callback_data}181"]
                ];
            }
    if (in_array("180",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['6'], 'callback_data' => "{$callback_data}180"]
                ];
            }
    if (in_array("365",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['365'], 'callback_data' => "{$callback_data}365"]
                ];
            }
    if (in_array("0",$montheproduct)){
                $monthkeyboard['inline_keyboard'][] = [
                    ['text' => $textbotlang['Admin']['month']['unlimited'], 'callback_data' => "{$callback_data}0"]
                ];
            }
    if($statusbtnextend)$monthkeyboard['inline_keyboard'][] = [['text' => "♻️ Extend current plan", 'callback_data' => "exntedagei"]];
    if ($statuscustomvolume == true)$monthkeyboard['inline_keyboard'][] = [['text' => $textbotlang['users']['customsellvolume']['title'], 'callback_data' => "customsellvolume"]];
    $monthkeyboard['inline_keyboard'][] = [
                ['text' => $textbotlang['users']['stateus']['backinfo'], 'callback_data' => $callback_data_back]
            ];
    return json_encode($monthkeyboard);
}
$Startelegram = json_encode([
    'keyboard' => [
        [['text' => "🗂 Star gateway name"]],
        [['text' => "💰 Star cashback"],['text' => "📚 Set Star guide"]],
        [['text' => "⬇️ Min amount Star"],['text' => "⬆️ Max amount Star"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$keyboardchangelimit = json_encode([
    'keyboard' => [
        [['text' => "🆓 Free limit"],['text' => "↙️ Global limit"]],
        [['text' => "🔄 Reset all user limits"]],
        [['text' => $textbotlang['Admin']['backadmin']]]
    ],
    'resize_keyboard' => true
]);
function KeyboardCategoryadmin(){
    global $pdo,$textbotlang;
    $stmt = $pdo->prepare("SELECT * FROM category");
    $stmt->execute();
    $list_category = [
        'keyboard' => [],
        'resize_keyboard' => true,
    ];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list_category['keyboard'][] = [['text' =>$row['remark']]];
    }
    $list_category['keyboard'][] = [
        ['text' => $textbotlang['Admin']['backadmin']],
    ];
    return json_encode($list_category);
}
$nowpayment_setting_keyboard = json_encode([
    'keyboard' => [
        [['text' => "API NOWPAYMENT"],['text' => "🗂 NowPayment gateway name"]],
        [['text' => "💰 NowPayment cashback"],['text' => "📚 Set NowPayment guide"]],
        [['text' => "⬇️ Min amount NowPayment"],['text' => "⬆️ Max amount NowPayment"]],
        [['text' => $textbotlang['Admin']['backadmin']],['text' => $textbotlang['Admin']['backmenu']]]
    ],
    'resize_keyboard' => true
]);
$Exception_auto_cart_keyboard = json_encode([
    'keyboard' => [
        [['text' => "➕ Add user exception"],['text' => "❌ Remove user from list"]],
        [['text' => "👁 Show user list"]],
        [['text' => "▶️ Back to card settings menu"]]
    ],
    'resize_keyboard' => true
]);
function keyboard_config($config_split,$id_invoice,$back_active = true){
    global $textbotlang;
    $keyboard_config = ['inline_keyboard' => []];
    $keyboard_config['inline_keyboard'][] = [
        ['text' => "⚙️ Config", 'callback_data' => "none"],
        ['text' => "✏️ Config name", 'callback_data' => "none"],
        ];
    for($i = 0; $i<count($config_split);$i++){
        $config = $config_split[$i];
        $split_config = explode("://",$config);
        $type_prtocol = $split_config[0];
        $split_config = $split_config[1];
        if(isBase64($split_config)){
            $split_config = base64_decode($split_config);
        }
        if($type_prtocol == "vmess"){
            $split_config = json_decode($split_config,true)['ps'];
        }elseif($type_prtocol == "ss"){
            $split_config = $split_config;
            $split_config = explode("#",$split_config)[1];
        }else{
        $split_config = explode("#",$split_config)[1];
        }
        $keyboard_config['inline_keyboard'][] = [
        ['text' => "Get config", 'callback_data' => "configget_{$id_invoice}_$i"],
        ['text' => urldecode($split_config), 'callback_data' => "none"],
        ];
        
    }
    $keyboard_config['inline_keyboard'][] = [['text' => "⚙️ Get all configs", 'callback_data' => "configget_$id_invoice"."_1520"]];
    if($back_active){
    $keyboard_config['inline_keyboard'][] = [['text' => $textbotlang['users']['stateus']['backinfo'], 'callback_data' => "product_$id_invoice"]];
    }
    return json_encode($keyboard_config);
}
$keyboard_buy = json_encode([
        'inline_keyboard' => [
            [
                ['text' => "🛍 Buy subscription", 'callback_data' => 'buy'],
            ],
        ]
    ]);
$keyboard_stat = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => "⏱️ All stats", 'callback_data' => 'stat_all_bot'],
                ],[
                    ['text' => "⏱️ Last hour", 'callback_data' => 'hoursago_stat'],
                ],
                [
                    ['text' => "⛅️ Today", 'callback_data' => 'today_stat'],
                    ['text' => "☀️ Yesterday", 'callback_data' => 'yesterday_stat'],
                ],
                [
                    ['text' => "☀️ Current month", 'callback_data' => 'month_current_stat'],
                    ['text' => "⛅️ Previous month", 'callback_data' => 'month_old_stat'],
                ],
                [
                    ['text' => "🗓 Stats by date range", 'callback_data' => 'view_stat_time'],
                ]
            ]
        ]);