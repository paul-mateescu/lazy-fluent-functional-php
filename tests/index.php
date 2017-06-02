<?php
require_once('../LFF7.php');

function ok_message($message){
    echo  "<div style=\"color:green\"><pre>OK - ", $message, "</pre></div>";
}

function failed_message($message){
    echo "<div style=\"color:red\"><pre>FAILED - ", $message, "</pre></div>";
}

function test($func, $message){
    $result = $func();
    if($result === TRUE)
        ok_message($message);
    else
        failed_message($message);
    return $result === TRUE;
}

test(
    function(){return \LFF7\from_array([1, 2, 3])->to_array() === [1, 2, 3];},
    '\LFF7\from_array([1, 2, 3])->to_array() === [1, 2, 3]'
);

test(
    function(){return \LFF7\from_array([])->to_array() === [];},
    '\LFF7\from_array([])->to_array() === []'
);

test(
    function(){return \LFF7\naturals()->take(3)->to_array() === [0, 1, 2];},
    'return \LFF7\naturals()->take(3)->to_array() === [0, 1, 2]'
);

