<?php
// Heading
$_['heading_title']      = 'Fondy (Visa/MasterCard)';

// Text 
$_['text_payment']       = 'Оплата';
$_['text_oplata']       	 = '<a onclick="window.open(\'http://oplata.com/\');"><img src="view/image/payment/oplata.png" alt="Oplata" title="Oplata" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_success']       = 'Настройки модуля обновлены!';   
$_['text_pay']           = 'Oplata';
$_['text_card']          = 'Visa/MasterCard';

// Entry
$_['entry_merchant']     = 'Merchant ID:';
$_['entry_secretkey']    = 'Secret key:';

$_['text_response_description']               = 'Текст ошибки:';
$_['text_oplata_order_status']                = 'Статус заказа:';
$_['text_response_code']                      = 'Код ошибки:';

$_['entry_order_status'] = 'Статус заказа после оплаты:';
$_['entry_order_process_status'] = 'Статус заказа в процессе:';
$_['entry_order_status_cancelled']    =  'Статус отмененного заказа:';
$_['entry_currency']     = 'Валюта мерчанта';
$_['entry_backref']      = 'Ссылка возврата клиента:<br /><span class="help">http://{your_domain}/index.php?route=payment/oplata/response</span>';
$_['entry_server_back']  = 'Ссылка возврата для сервера:<br /><span class="help">http://{your_domain}/index.php?route=payment/oplata/callback</span>';
$_['entry_language']     = 'Язык страницы:<br /><span class="help">по-умолчанию : RU </span>';

$_['entry_status']       = 'Статус:';
$_['entry_sort_order']   = 'Порядок сортировки:';

// Error
$_['error_permission']   = 'У Вас нет прав для управления этим модулем!';
$_['error_merchant']     = 'Неверный ID магазина (Merchant ID)!';
$_['error_secretkey']    = 'Отсутствует секретный ключ!';
?>