# lazy-fluent-functional-php

This is a lazy, fluent and functional library for PHP 7. 

I have one for PHP 5 in the works as well :)

Please bear with me, I will update this, create test files etc as fast and as often I can.



## Examples

__Factorial function:__

```php
function factorial($n){
    return \LFF7\from_range(1,$n) -> product();
}
```

__Finding the sum of the first one million natural numbers:__

```php
$sum = 
    \LFF7\naturals()
        ->take(1000000)
        ->sum();
```
__Finding the sum of all the values in the third column of a CSV file:__

```php
$sum = 
    \LFF7\from_csv_file('myfile.csv')
        ->column(2)
        ->sum();
```
(It will work with a 1TB file, because it's all lazy :) )

__Finding the product of the even numbers of an array:__

```php
$product = 
    \LFF7\from_array([1, 5, 7, 9, 0, 2, 3, 6, 5, 2, 6])
        ->evens()
        ->product();
```
(It will stop after finding a zero and return it as the final value of the computation).

__A complex computation from a CSV file:__

Let's say we have a CSV file with the following structure:

|Name| Date|Sales|
|----|-----|-----:|
|Danny|2017-01-03|100.25|
|George|2017-01-03|90.00|
|Linda|2017-01-03|190.00|
|Danny|2017-01-04|200.00|
|George|2017-01-04|254.00|
|Linda|2017-01-04|810.99|
|...|...|...|
We would like to obtain the sum of the sales on each person, ordered by name. 
 

```php
$arr = 
    \LFF7\from_csv_file('sales.csv')
        ->columns(0, 2)
        ->reindex()
        ->group_on_column(0)
        ->sort_asc_on_key()
        ->map(
            function($v){
                return 
                \LFF\from_array($v)
                    ->column(1)
                    ->sum();
            }
        )
        ->to_array();

```

## Installation

Just require `LFF7.php`.

## Usage

### Creating generators

__From an array__:

`$gen = \LFF7\from_array([1, 2, 3, 4, 5]);`

__From a range of integers__:

`$gen = \LFF7\from_range(10, 2000000);`

(the range of integers starting with 10 and ending with 2000000)

__All natural numbers:__

`$gen = \LFF7\naturals();`

(returns a generator lazily yielding  all natural numbers)

__From a file:__

`$gen = \LFF7\from_file('myfile.txt');`

(returns a generator that lazily yields one row at a time, as string)

__From a CSV file:__

`$gen = \LFF7\from_csv_file('myfile.csv');`

(returns a generator that lazily yields one row at a time, as an array)

...the rest is coming soon
