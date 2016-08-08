<?php
namespace WebX\Ioc\Impl;

function readArray($key,array $array = null) {
    return isset($array[$key]) ? $array[$key] : null;
}