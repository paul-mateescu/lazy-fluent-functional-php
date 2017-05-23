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

...the rest is coming soon
