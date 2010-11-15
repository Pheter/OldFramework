<?php

function array_to_object($array) {
    
    return is_array($array) ? (object)array_map(__FUNCTION__, $array) : $array;
}
