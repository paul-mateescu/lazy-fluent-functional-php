# lazy-fluent-functional-php

This is a lazy, fluent and functional library for PHP 7. 

I have one for PHP 5 in the works as well :)

Please bear with me, I will update this, create test files etc as fast and as often I can.

# Contents
[**Examples**](#examples)

[**Installation**](#installation)

[**Usage**](#usage)

[**Reference**](#reference)

[drop(int $noOfRowsToDrop)](#drop)

[drop_until(callable $predicate)](#drop-until)

[map(callable $transform)](#map)

[take(int $noOfRowsToTake)](#take)

[take_while(callable $predicate)](#take-while)

[filter(callable $predicate)](#filter)

[exclude(callable $predicate)](#exclude)

[column($column_key)](#column)

[columns(...$column_keys)](#columns)

[delete_column($column_key)](#delete-column)

[reindex()](#reindex)

[key_from_column($column_key)](#key-from-column)

[sort_asc_on_key()](#)

[sort_desc_on_key()](#)

[sort_desc_on_column($column_key)](#)

[group_on_column($column_key)](#)

[group_on_key()](#)

[all(callable $predicate)](#)

[any(callable $predicate)](#)

[to_array()](#)

[reduce(callable $reduce_function)](#)

[sum()](#)

[product()](#)

[odds()](#)

[evens()](#)

[vowels()](#)

[consonants()](#)


# [Examples](#contents)

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

## [Installation](#contents)

Just require `LFF7.php`.

## [Usage](#contents)

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

# [Reference](#contents)

#### <a name="drop"></a> drop(int $noOf<a name=""></a>RowsToDrop)

Returns a new generator that drops `$noOfRowsToDrop` rows from the current one. *Lazy/Fluent*

#### <a name="drop-until"></a>drop_until(callable $predicate)

Returns a new generator that drops rows from the current one until `$predicate` returns `true`. *Lazy/Fluent*

#### <a name="map"></a>map(callable $transform)
#### <a name="take"></a>take(int $noOfRowsToTake)
#### <a name="take-while"></a>take_while(callable $predicate)
#### <a name="filter"></a>filter(callable $predicate)
#### <a name="exclude"></a>exclude(callable $predicate)
#### <a name="column"></a>column($column_key)
#### <a name="columns"></a>columns(...$column_keys)
#### <a name="delete-column"></a>delete_column($column_key)
#### <a name="reindex"></a>reindex()
#### <a name="key-from-column"></a>key_from_column($column_key)
#### <a name="sort-asc-on-key"></a>sort_asc_on_key()
#### <a name="sort-desc-on-key"></a>sort_desc_on_key()
#### <a name="sort-desc-on-column"></a>sort_desc_on_column($column_key)
#### <a name="group-on-column"></a>group_on_column($column_key)
#### <a name="group-on-key"></a>group_on_key()
#### <a name="all"></a>all(callable $predicate)
#### <a name="any"></a>any(callable $predicate)
#### <a name="to-array"></a>to_array()
#### <a name="reduce"></a>reduce(callable $reduce_function)
#### <a name="sum"></a>sum()
#### <a name="product"></a>product()
#### <a name="odds"></a>odds()
#### <a name="evens"></a>evens()
#### <a name="vowels"></a>vowels()
#### <a name="consonants"></a>consonants()

