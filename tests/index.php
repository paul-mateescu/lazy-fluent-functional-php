<?php
require_once('../LFF7.php');

function test($func, $message){
    $result = $func();
    echo "<pre>";
    if($result === TRUE)
        echo  "OK - ", $message, "\n";
    else
        echo "FAILED - ", $message, "\n";
    echo "</pre>";
    return $result === TRUE;
}

test(
    function(){
        return \LFF7\from_array([1, 2, 3])->to_array() === [1, 2, 3];
    },
    '\LFF7\from_array([1, 2, 3])->to_array() === [1, 2, 3]'
);

test(
    function(){
        return \LFF7\from_array([])->to_array() === [];
    },
    '\LFF7\from_array([])->to_array() === []'
);

test(
    function(){
        return \LFF7\naturals()->take(3)->to_array() === [0, 1, 2];
    },
    'return \LFF7\naturals()->take(3)->to_array() === [0, 1, 2]'
);
