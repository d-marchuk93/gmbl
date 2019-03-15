<?php
require './vendor/autoload.php';

$env = new \Symfony\Component\Dotenv\Dotenv();
$env->load('.env');

$vo = gumballChangeValue('t1', 'change_type', ['new' => 'data in some']);
var_dump($vo);

$new = gumballPutValue('t2', 'new_type', ['data' => 'new'], false);
var_dump($new);

$created = gumballGetValue('t2', 'new_type');
var_dump($created);

$canged = gumballChangeValue('t2', 'new_type', ['edit' => 'data']);
var_dump($canged);

$remove = gumballRemoveValue('t2', 'new_type');
var_dump($remove);

$remove = gumballRemoveValue('t2', 'new_type');
var_dump($remove);
