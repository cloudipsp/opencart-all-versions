#EN

Installation
-------------
1. Upload all of the contents in the "upload" directory to the root directory of your shop (if installed custom theme - folder "payment" from /catalog/view/theme/default/template/payment/ copy in folder of your custom theme)
2. Go to the admin panel, select menu "Extensions" -> "Modifications" than click "Refresh" button
3. In list of all the available payment methods find "Fondy"
4. Click "Install"
5. Click the "Edit"
6. Fill in the required fields, and save the changes

If Fondy scripts not included you need to add the following script on cart page -

```javascript
<script src="https://api.fondy.eu/static_common/v1/checkout/ipsp.js"></script>
```

-------------

#RU

Установка
-------------
1. Скопировать все из папки upload в корень сайта (если установлена сторонняя тема папку payment из /catalog/view/theme/default/template/payment/ скопировать в папку с темой)
2. Зайти в админку сайта. Выбрать меню "Дополнения" -> "Менеджер дополнений" нажать кнопку обновить
3. В списке оплат найти способ "Fondy"
4. Нажать кнопку "Установить"
5. Нажать кнопку "Изменить"
6. Заполнить необходимые поля и сохранить изменения.

Если "Менеджер дополнений" не сработал, скрипт добавить в ручную на страницу корзины -

```javascript
<script src="https://api.fondy.eu/static_common/v1/checkout/ipsp.js"></script>
```

-------------

Styles :


		'html , body' : {
			'overflow' : 'hidden',
			'height' : 'auto'
		},'.col.col-shoplogo' : {
			'display' : 'none'
		},
		'.col.col-language' : {
			'display' : 'none'
		},
		'.pages-checkout' : {
			'background' : 'transparent'
		},
		'.col.col-login' : {
			'display' : 'none'
		},
		'.pages-checkout .page-section-overview' : {
			'background' : '#fff',
			'color' : '#252525',
			'border-bottom' : '1px solid #dfdfdf'
		},
		'.col.col-value.order-content' : {
			'color' : '#252525'
		},
		'.page-section-footer' : {
			'display' : 'none'
		},
		'.page-section-tabs' : {
			'display' : 'none'
		},

		'.page-section-shopinfo' : {
			'display': 'none'
		},
		
		'.page-section-overview' : {
			'display': 'none'
		},