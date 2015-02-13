<?php

\Yii::$container->set('yii\redactor\widgets\Redactor', ['clientOptions' => [
    'lang' => 'ru',
    'buttons' => [
        'formatting',  'bold', 'italic', 'deleted',
        'unorderedlist', 'orderedlist', 'outdent', 'indent',
        'file', 'table', 'link', 'alignment', 'horizontalrule'
    ],
    'minHeight' => 150,
]]);