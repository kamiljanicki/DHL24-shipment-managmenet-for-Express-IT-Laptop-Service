<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'controllers');
set_include_path(get_include_path() . PATH_SEPARATOR . 'models');
set_include_path(get_include_path() . PATH_SEPARATOR . 'views');
set_include_path(get_include_path() . PATH_SEPARATOR . 'helpers');
set_include_path(get_include_path() . PATH_SEPARATOR . 'data');
set_include_path(get_include_path() . PATH_SEPARATOR . 'includes');

function class_loader($class)
{
    include $class . '.php';
}

spl_autoload_register('class_loader');