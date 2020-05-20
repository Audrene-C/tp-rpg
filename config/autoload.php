<?php

function loadClass($class)
{
    require_once('./classes/'.$class.'.php');
}


spl_autoload_register('loadClass');