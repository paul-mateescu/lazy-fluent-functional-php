# lazy-fluent-functional-php

Hello, this is my attempt at creating a lazy, fluent and functional library for PHP 7. 

I have one for PHP 5 in the works as well :)

Please bear with me, I will update this, create test files etc as fast and as often I can.

Do not consider it production-quality code just yet. 

This library will allow you to have functions like this:

```php
function factorial($n){
    return \LFF7\from_range(1,$n) -> product();
}
```
Or, if you have a CSV file and you want to sum all the values in the third column, you can whip a one-liner:

```php
$sum = \LFF7\from_csv_file('myfile.csv')->column(2)->sum();
```
(It will work with a 1TB file, because it's all lazy :) )
