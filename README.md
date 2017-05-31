# lazy-fluent-functional-php

This is a lazy, fluent and functional library for PHP 7. 

I have one for PHP 5 in the works as well :)

Please bear with me, I will update this, create test files etc as fast and as often I can.

# Contents
[**Examples**](#examples)

[**Installation**](#installation)

[**Usage**](#usage)

<a name="contents-reference"></a>[**Reference**](#reference)

+ [drop(int $noOfRowsToDrop)](#drop)
+ [drop_until(callable $predicate)](#drop-until)
+ [map(callable $transform)](#map)
+ [take(int $noOfRowsToTake)](#take)
+ [take_while(callable $predicate)](#take-while)
+ [filter(callable $predicate)](#filter)
+ [exclude(callable $predicate)](#exclude)
+ [column($column_key)](#column)
+ [columns(...$column_keys)](#columns)
+ [delete_column($column_key)](#delete-column)
+ [reindex()](#reindex)
+ [key_from_column($column_key)](#key-from-column)
+ [sort_asc_on_key()](#)
+ [sort_desc_on_key()](#)
+ [sort_desc_on_column($column_key)](#)
+ [group_on_column($column_key)](#)
+ [group_on_key()](#)
+ [all(callable $predicate)](#)
+ [any(callable $predicate)](#)
+ [to_array()](#)
+ [reduce(callable $reduce_function)](#)
+ [sum()](#)
+ [product()](#)
+ [odds()](#)
+ [evens()](#)
+ [vowels()](#)
+ [consonants()](#)


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

1. Obtain a generator wrapped in an object using the generator creation functions `from_array`, `from_file` a.s.o. Example:

```php
$generator = from_array([1, 2, 3, 4, 5, 6, 7, 8, 9]);
```

2. Fluently chain methods that express your desired computation. For example:

```php
$computation = $generator->odds()->less_than(6);
```

3. Perform the actual computation (note the function invocation):

```php
foreach($computation() as $value){
    echo $value, " ";
}
```

### Functions that return generator objects

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

#### <a name="drop"></a> [drop(int $noOfRowsToDrop)](#contents)

Returns a new generator that drops the first `$noOfRowsToDrop` rows from the current generator. *Lazy/Fluent*

**Example**: `\LFF7\from_array([1, 2, 3, 4, 5])->drop(2)->to_array()` produces the array `[3, 4, 5]`.

#### <a name="drop-until"></a>[drop_until(callable $predicate)](#contents)

Returns a new generator that drops rows from the current one until `$predicate` returns `true`. *Lazy/Fluent*

**Example**: `\LFF7\from_array([1, 2, 3, 4, 5])->drop_until(function($n){return $n > 3;})->to_array()` produces the array `[4, 5]`.

#### <a name="map"></a> [map(callable $transform)](#contents-reference)

Returns a new generator that applies the `$transform` callable to items yielded from the current generator. *Lazy/Fluent*

**Example**: `\LFF7\from_array([1, 2, 3, 4, 5])->map(function($x){return $x++;})->to_array()` produces the array `[2, 3, 4, 5, 6]`.

#### <a name="take"></a>[take(int $noOfRowsToTake)](#contents-reference)

Returns a new generator that will yield at most `$noOfRowsToTake` from the current generator. *Lazy/Fluent*

**Example**: `\LFF7\naturals()->take(100)` will yield the first 100 natural numbers, i.e 0, 1, 2, ..., 99.

#### <a name="take-while"></a>[take_while(callable $predicate)](#contents-reference)

Returns a new generator that yields rows from the current one while `$predicate` returns `true`. *Lazy/Fluent*

**Example**: `\LFF7\from_array([1, 2, 3, 4, 5])->take_while(function($n){return $n < 3;})->to_array()` produces the array `[1, 2]`.

#### <a name="filter"></a>[filter(callable $predicate)](#contents-reference)

Returns a new generator that yields rows from the current one for which `$predicate` returns `true`. *Lazy/Fluent*

**Example**: `\LFF7\from_array([1, 2, 3, 4, 5])->filter(function($n){return $n % 2 == 0;})->to_array()` produces the array `[2, 4]`.

(Please note that method `evens()` does the same thing.)

#### <a name="exclude"></a>[exclude(callable $predicate)](#contents-reference)

Returns a new generator that yields rows from the current one for which `$predicate` returns `false`. *Lazy/Fluent*

**Example**: `\LFF7\from_array([1, 2, 3, 4, 5])->exclude(function($n){return $n % 2 == 0;})->to_array()` produces the array `[1, 3, 5]`.

#### <a name="column"></a>[column($column_key)](#contents-reference)

Returns a new generator that yields only the column with the `$column_key` key from what the current generator yields, considering that every item yielded is an array. *Lazy/Fluent*

Please note that, since we are interested in just a column, this method does NOT yield an array with one element, it yields the desired column value directly. The key information is lost.

**Example**: `\LFF7\from_array([1, 2], [3, 4], [5, 6]])->column(0)->to_array()` produces the array `[1, 3, 5]`.

#### <a name="columns"></a>[columns(...$column_keys)](#contents-reference)

Returns a new generator that yields only the columns with the keys given in `$column_keys` from what the current generator yields, considering that every item yielded is an array. *Lazy/Fluent*

Key information is preserved.

**Example**: `\LFF7\from_array([1, 2, 7], [3, 4, 8], [5, 6, 9]])->columns(1, 2)->to_array()` produces the array `[1=>2, 2=>7], [1=>4, 2=>8], [1=>6, 2=>9]]`.

#### <a name="delete-column"></a>[delete_column($column_key)](#contents-reference)

Returns a new generator that yields only the columns with the keys given in `$column_keys` from what the current generator yields, considering that every item yielded is an array. *Lazy/Fluent*

Key information is preserved.

#### <a name="reindex"></a>[reindex()](#contents-reference)
#### <a name="key-from-column"></a>[key_from_column($column_key)](#contents-reference)
#### <a name="sort-asc-on-key"></a>[sort_asc_on_key()](#contents-reference)
#### <a name="sort-desc-on-key"></a>[sort_desc_on_key()](#contents-reference)
#### <a name="sort-desc-on-column"></a>[sort_desc_on_column($column_key)](#contents-reference)
#### <a name="group-on-column"></a>[group_on_column($column_key)](#contents-reference)
#### <a name="group-on-key"></a>[group_on_key()](#contents-reference)
#### <a name="all"></a>[all(callable $predicate)](#contents-reference)
#### <a name="any"></a>[any(callable $predicate)](#contents-reference)
#### <a name="to-array"></a>[to_array()](#contents-reference)
#### <a name="reduce"></a>[reduce(callable $reduce_function)](#contents-reference)
#### <a name="sum"></a>[sum()](#contents-reference)
#### <a name="product"></a>[product()](#contents-reference)
#### <a name="odds"></a>[odds()](#contents-reference)
#### <a name="evens"></a>[evens()](#contents-reference)
#### <a name="vowels"></a>[vowels()](#contents-reference)
#### <a name="consonants"></a>[consonants()](#contents-reference)

