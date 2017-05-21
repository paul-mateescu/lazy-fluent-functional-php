# lazy-fluent-functional-php

Hello, this is my attempt at creating a lazy, fluent and functional library for PHP 7. 

I have one for PHP 5 in the works as well :)

Please bear with me, I will update this, create test files etc as fast and as often I can.

Do not consider it production-quality code just yet. 

This library will aloow you to have functions like this:

function factorial($n){

    return \LFF7\from_range(1,$n) -> product();

}

