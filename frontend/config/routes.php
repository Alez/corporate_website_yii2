<?php
return [
    // Каталог
    '<_a:products>' => 'catalog/product/<_a>',
    '<_a:(products|product)>/<slug>' => 'catalog/product/<_a>',

    // Новости
    'news' => 'news/news/index',
    '<_c:news>/<slug>' => 'news/<_c>/view',

    // Юзер
    '<_a:(signup|login|profile)>' => 'user/default/<_a>',

    // Остальные страницы
    '<slug:[\w\-_]+>' => 'pages/default/page',
];