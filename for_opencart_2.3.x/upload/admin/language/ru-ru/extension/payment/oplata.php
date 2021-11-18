<?php
// Heading
$_['heading_title'] = 'Fondy';

// Tab
$_['tab_general'] = 'Основные настройки';
$_['tab_order_status'] = 'Статус заказа';
    $_['tab_style'] = 'Стили';

// Text
$_['text_edit'] = 'Редакторование модуля';
$_['text_payment'] = 'Оплата';
$_['text_oplata'] = '<a onclick="window.open(\'http://fondy.eu\');"><img src="view/image/payment/oplata.png" alt="Fondy" title="Fondy" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_success'] = 'Настройки модуля обновлены!';
$_['text_pay'] = 'Fondy';
$_['text_card'] = 'Visa/MasterCard';
$_['text_response_description'] = 'Текст ошибки:';
$_['text_oplata_order_status'] = 'Статус заказа:';
$_['text_response_code'] = 'Код ошибки:';
$_['text_payment_details'] = 'Платёжные реквизиты Fondy';
$_['text_fondy_order_id'] = 'ID заказа Fondy';
$_['text_payment_id'] = 'ID платежа';
$_['text_order_total'] = 'Сумма заказа';
$_['text_masked_card'] = 'Маскированный номер карты';
$_['text_order_status'] = 'Статус заказа';
$_['text_order_last_tran_type'] = 'Тип последней транзакции';
$_['text_charge_the_amount'] = 'Списание заблокированной суммы';
$_['text_total'] = 'Введите сумму';
$_['text_refund'] = 'Возврат';
$_['text_success_action'] = 'Успешно!';

// Entry
$_['entry_env'] = 'Окружение';
$_['entry_merchant'] = 'Merchant ID';
$_['entry_secretkey'] = 'Secret key';
$_['entry_geo_zone'] = 'Географическая зона';
$_['entry_order_success_status'] = 'Статус оплаченного заказа';
$_['entry_order_process_status'] = 'Статус заказа в обработке';
$_['entry_order_cancelled_status'] = 'Статус отмененного заказа';
$_['entry_order_reverse_status'] = 'Статус возвращенного заказа';
$_['entry_currency'] = 'Валюта мерчанта';
$_['entry_payment_type'] = 'Тип платежа';
$_['entry_common_type'] = 'Покупка';
$_['entry_preauth_type'] = 'Предавторизация';
$_['entry_status'] = 'Статус';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_styles'] = 'Стили';
$_['entry_process_payment_type'] = 'Способ интеграции';
$_['entry_redirect'] = 'Перенаправление на Fondy';
$_['entry_built_in_checkout'] = 'Встроенная форма в корзине';
$_['entry_style_theme'] = 'Тема';
$_['entry_light'] = 'Светлая';
$_['entry_dark'] = 'Тёмная';
$_['entry_style_preset'] = 'Пресет';

// Btn
$_['btn_capture'] = 'Списать';
$_['btn_preauth_reverse'] = 'Отмена предавторизации';
$_['btn_reverse'] = 'Возврат';

// Error
$_['error_permission'] = 'У Вас нет прав для управления этим модулем!';
$_['error_merchant'] = 'Неверный ID магазина (Merchant ID)!';
$_['error_secretkey'] = 'Отсутствует секретный ключ!';

// Tooltip
$_['tooltip_entry_status'] = 'Отображать Fondy в списке платежных методов на сайте';
$_['tooltip_entry_environment'] = 'Выберите \'Test\' для тестирования Fondy';
$_['tooltip_entry_merchant'] = 'Можно найти в портале Fondy (раздел \'Настройки мерчанта\' → \'Технические\')';
$_['tooltip_entry_secretkey'] = 'Можно найти в портале Fondy (раздел \'Настройки мерчанта\' → \'Технические\')';
$_['tooltip_entry_process_payment_type'] = 'Выберите где будет отображаться платежная страница Fondy';
$_['tooltip_entry_payment_type'] = 'При выборе \'' . $_['entry_preauth_type'] . '\' сумма заказа блокируется на карте плательщика';
$_['tooltip_field_payment_id'] = 'Уникальный идентификатор платежа, присвоенный платежным шлюзом FONDY';
$_['tooltip_btn_preauth_reverse'] = 'Доступно только на всю сумму заказа';

$_['help_entry_environment'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Вам присвоен <a href="https://docs.fondy.eu/docs/page/2/">тестовый</a> <em>' . $_['entry_merchant'] . '</em> и <em>' . $_['entry_secretkey'] . '</em>';
?>