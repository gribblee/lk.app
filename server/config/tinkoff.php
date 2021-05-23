<?php
return [
    'terminalKey' => env('TINKOFF_TERMINAL'),
    'secretKey' => env('TINKOFF_SECRET'),
    'url' => env('TINKOFF_URL', 'https://securepay.tinkoff.ru/v2/'),
    'showcase_id' => env('TINKOFF_SHOWCASE_ID', ''),
    'shop_id' => env('TINKOFF_SHOP_ID', ''),
    'credit_url' => env('TINKOFF_CREDIT_URL', 'https://forma.tinkoff.ru/api/partners/v2/orders/create'),
    'lang' => 'ru',
    'description' => 'Пополнение личного счета в сервисе leadz.monster',
    'taxation' => 'usn_income', //osn — общая, usn_income — упрощенная (доходы), usn_income_outcome — упрощенная (доходы минус расходы), patent — патентная, envd — единый налог на вмененный доход, esn — единый сельскохозяйственный налог
    'nds' => 'none', //none — без НДС, vat0 — 0%, vat10 — 10%, vat20 — 20%, vat110 — 10/110, vat120 — 20/120
];
