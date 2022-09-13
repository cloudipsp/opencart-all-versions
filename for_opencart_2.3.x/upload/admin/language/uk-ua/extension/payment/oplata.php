<?php
// Heading
$_['heading_title'] = 'Fondy';

// Tab
$_['tab_general'] = 'Основні налаштування';
$_['tab_order_status'] = 'Статус замовлення';
$_['tab_style'] = 'Стилі';

// Text
$_['text_edit'] = 'Редагування модуля';
$_['text_payment'] = 'Оплата';
$_['text_oplata'] = '<a onclick="window.open(\'https://fondy.io\');"><img src="view/image/payment/oplata.png" alt="Fondy" title="Fondy" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_success'] = 'Налаштування модуля оновлено!';
$_['text_pay'] = 'Fondy';
$_['text_card'] = 'Visa/MasterCard';
$_['text_response_description'] = 'Текст помилки:';
$_['text_oplata_order_status'] = 'Статус замовлення:';
$_['text_response_code'] = 'Код помилки:';
$_['text_payment_details'] = 'Платіжні реквізити Fondy';
$_['text_fondy_order_id'] = 'ID замовлення Fondy';
$_['text_payment_id'] = 'ID платежу';
$_['text_order_total'] = 'Сумма замовлення';
$_['text_masked_card'] = 'Маскований номер картки';
$_['text_order_status'] = 'Статус замовлення';
$_['text_order_last_tran_type'] = 'Тип останньої транзакції';
$_['text_charge_the_amount'] = 'Списання заблокованої суми';
$_['text_total'] = 'Введіть суму';
$_['text_refund'] = 'Повернення';
$_['text_success_action'] = 'Успішно!';

// Entry
$_['entry_env'] = 'Cередовище';
$_['entry_merchant'] = 'Merchant ID';
$_['entry_secretkey'] = 'Secret key';
$_['entry_geo_zone'] = 'Географічна зона';
$_['entry_order_success_status'] = 'Статус оплаченого замовлення';
$_['entry_order_process_status'] = 'Статус замовлення в обробці';
$_['entry_order_cancelled_status'] = 'Статус скасованого замовлення';
$_['entry_order_reverse_status'] = 'Статус поверненого замовлення';
$_['entry_currency'] = 'Валюта мерчанта';
$_['entry_payment_type'] = 'Тип платежу';
$_['entry_common_type'] = 'Купівля';
$_['entry_preauth_type'] = 'Передавторизація';
$_['entry_status'] = 'Статус';
$_['entry_sort_order'] = 'Порядок сортування';
$_['entry_styles'] = 'Стилі';
$_['entry_process_payment_type'] = 'Спосіб інтеграції';
$_['entry_redirect'] = 'Перенаправлення на Fondy';
$_['entry_built_in_checkout'] = 'Вбудована форма в кошику';
$_['entry_style_theme'] = 'Тема';
$_['entry_light'] = 'Світла';
$_['entry_dark'] = 'Темна';
$_['entry_style_preset'] = 'Пресет';

// Btn
$_['btn_capture'] = 'Списати';
$_['btn_preauth_reverse'] = 'Скасування передавторизації';
$_['btn_reverse'] = 'Повернення';

// Error
$_['error_permission'] = 'У вас немає прав для керування цим модулем!';
$_['error_merchant'] = 'Невірний ID магазину (Merchant ID)!';
$_['error_secretkey'] = 'Немає секретного ключа!';

// Tooltip
$_['tooltip_entry_status'] = 'Відображати Fondy у списку платіжних методів на сайті';
$_['tooltip_entry_environment'] = 'Оберіть \'Test\' для тестування Fondy';
$_['tooltip_entry_merchant'] = 'Можна знайти у порталі Fondy (розділ \'Налаштування мерчанта\' → \'Технічні налаштування\')';
$_['tooltip_entry_secretkey'] = $_['tooltip_entry_merchant'];
$_['tooltip_entry_process_payment_type'] = 'Оберіть, де саме буде відображатись платіжна сторінка Fondy';
$_['tooltip_entry_payment_type'] = 'При виборі \'' . $_['entry_preauth_type'] . '\' сума замовлення блокується на картці платника';
$_['tooltip_field_payment_id'] = 'Унікальний ідентифікатор платежу, присвоєний платіжним шлюзом Fondy';
$_['tooltip_btn_preauth_reverse'] = 'Доступно тільки на всю суму замовлення';

$_['help_entry_environment'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Вам присвоєно <a href="https://docs.fondy.eu/docs/page/2/">тестовий</a> <em>' . $_['entry_merchant'] . '</em> і <em>' . $_['entry_secretkey'] . '</em>';
?>