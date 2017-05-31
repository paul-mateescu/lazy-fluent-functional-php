<?php
namespace LFF7;

class LazyFluentFunctional {
    private $generator;

    private function __construct($generator){
        $this->generator = $generator;
    }

    public function __invoke(){
        return ($this->generator)();
    }

    public function drop($noOfRowsToDrop){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $noOfRowsToDrop){
                foreach(($gen = $generator()) as $key => $value){
                    if($noOfRowsToDrop-- > 0) continue;
                    if(yield $key => $value) {
                        $gen->send(true); // halts the inner generator also;
                        return;
                    }
                }
            };
        return $this;
    }

    public function drop_while(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $predicate){
                $toDrop = true;
                foreach(($gen = $generator()) as $key => $value){
                    if($toDrop){
                        $toDrop = $predicate($value, $key);
                        if($toDrop) continue;
                    }
                    if(yield $key => $value){
                        $gen->send(true);
                        return;
                    } 
                }
            };
        return $this;


    }

    public function drop_until(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $predicate){
                $toDrop = true;
                foreach(($gen = $generator()) as $key => $value){
                    if($toDrop){
                        $toDrop = ! $predicate($value, $key);
                        if($toDrop) continue;
                    }
                    if(yield $key => $value) {
                        $gen->send(true);
                        return;
                    }
                }
            };
        return $this;
    }

    public function map(callable $transform){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $transform){
                foreach(($gen = $generator()) as $key => $value)
                    if(yield $key => $transform($value)) {
                        $gen->send(true);
                        return;
                    }
            };
        return $this;
    }

    public function take($noOfRowsToTake){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $noOfRowsToTake){
                foreach(($gen = $generator()) as $key => $value){
                    if($noOfRowsToTake-- <= 0) return;
                    if(yield $key => $value) {
                        $gen->send(true);
                        return;
                    }
                }
            };
        return $this;
    }

    public function take_while(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach(($gen = $generator()) as $key => $value){
                    if(! $predicate($value, $key)) return;
                    if(yield $key => $value) {
                        $gen->send(true);
                        return;
                    }
                }
            };
        return $this;
    }

    public function take_until(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach(($gen = $generator()) as $key => $value){
                    if($predicate($value, $key)) return;
                    if(yield $key => $value){
                        $gen->send(true);
                        return;
                    } 
                    }
                };
        return $this;
    }

    public function filter(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach(($gen = $generator()) as $key => $value){
                    if($predicate($value, $key)) 
                        if(yield $key => $value){
                            $gen->send(true);
                            return;
                        } 
                }
            };
        return $this;
    }

    public function exclude(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach(($gen = $generator()) as $key => $value){
                    if(!$predicate($value, $key)) 
                        if(yield $key => $value){
                            $gen->send(true);
                            return;
                        } 
                }
            };
        return $this;
    }

    public function column($column_key){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $column_key){
                    foreach(($gen = $generator()) as $key => $value)
                        if(yield $key => $value[$column_key]){
                            $gen->send(true);
                            return;
                        } 
                    };
        return $this;
    }

    function columns(...$column_keys){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $column_keys){
                foreach(($gen = $generator()) as $key => $value){
                    $arr = [];
                    foreach($column_keys as $column_key) 
                        $arr [$column_key]= $value[$column_key];
                    if(yield $key => $arr){
                        $gen->send(true);
                        return;
                    } 
                }
            };
        return $this;
    }

    function delete_columns(...$column_keys){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $column_key){
                    foreach(($gen = $generator()) as $key => $value){
			foreach($column_keys as $column_key)
                        	unset($value[$column_key]);
                        if(yield $key => $value){
                            $gen->send(true);
                            return;
                        } 
                    }
             };
        return $this;
    }

    function reindex(){
        $generator = $this->generator;
        $this->generator = 
                function() use ($generator){
                    $i = 0;
                    foreach(($gen = $generator()) as $key => $value)
                        if(yield $i++  => $value){
                            $gen->send(true);
                            return;
                        } 
                    }
                ;
        return $this;
    }

    function key_from_column($column_key){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $column_key){
                    foreach(($gen = $generator()) as $value)
                        if(yield $value[$column_key] => $value){
                            $gen->send(true);
                            return;
                        } 
                    };
        return $this;
    }

    function sort_asc_on_key(){
        //THIS IS NOT LAZY!!!! 
        //It requires all rows to fit in memory
        $generator = $this->generator;
        $this->generator = 
            function()use($generator){
                    $arr = [];
                    foreach(($gen = $generator()) as $key => $value)
                        $arr[$key] = $value;
                    ksort($arr);
                    foreach($arr as $key => $elem)
                        if(yield $key => $elem) return;
                    };
        return $this;
    }

    function sort_desc_on_key(){
        //THIS IS NOT LAZY!!!! 
        //It requires all rows to fit in memory
        $generator = $this->generator;
        $this->generator = 
            function()use($generator){
                    $arr = [];
                    foreach(($gen = $generator()) as $key => $value)
                        $arr[$key] = $value;
                    krsort($arr);
                    foreach($arr as $key => $elem)
                        if(yield $key => $elem) return;
                    };
        return $this;
    }

    function sort_desc_on_column($column_key){
        //THIS IS NOT LAZY!!!! 
        //It requires all rows to fit in memory
        $generator = $this->generator;
        $this->generator = 
            function()use($generator, $column_key){
                    $arr = [];
                    foreach(($gen = $generator()) as $key => $value)
                        $arr[$value[$column_key]] = [$key, $value];
                    krsort($arr);
                    foreach($arr as $elem)
                        if(yield $elem[0] => $elem[1]) return;
                    };
        return $this;
    }

    function group_on_column($column_key){
        //THIS IS NOT LAZY!!!! 
        //It requires all rows to fit in memory
        $generator = $this->generator;
        $this->generator = 
             function() use ($generator, $column_key){
                    $arr = [];
                    foreach(($gen = $generator()) as $value)
                        if(array_key_exists($value[$column_key], $arr))
                            $arr[$value[$column_key]][] = $value;
                        else
                            $arr[$value[$column_key]] = [$value];
                    foreach($arr as $key => $elem)
                        if(yield $key => $elem) return;
                    };
        return $this;
    }

    function group_on_key(){
        //THIS IS NOT LAZY!!!! 
        //It requires all rows to fit in memory
        $generator = $this->generator;
        $this->generator = 
            function()use($generator) {
                    $arr = [];
                    foreach(($gen = $generator()) as $key => $value)
                        if(array_key_exists($key, $arr))
                            $arr[$key][] = $value;
                        else
                            $arr[$key] = [$value];
                    foreach($arr as $key => $elem)
                        if(yield $key => $elem) return;
                    };
        return $this;
    }

    function all(callable $predicate){
        foreach(($this->generator)() as $key => $value)
            if(!$predicate($value, $key))
                return false;
            return true;
    }

    function any(callable $predicate){
        foreach(($this->generator)() as $key => $value)
            if($predicate($value, $key))
                return true;
            return false;
    }

	function to_array(){
        //THIS IS NOT LAZY!!!! 
        //It requires all rows to fit in memory
        return iterator_to_array(($this->generator)());
    }

    function reduce(callable $reduce_function){
        $first = true;
        foreach(($this->generator)() as $value)
            if($first){
                $result = $value;
                $first = false;
            }
            else
                $result = $reduce_function($result, $value);
        return $result;
    }
    
    function sum(){
        $result = 0;
        foreach(($this->generator)() as $value)
            $result += $value;
        return $result;
    }

    function product(){
        $result = 1;
        foreach(($this->generator)() as $value){
            if($value == 0)
                return 0;
            else 
                $result *= $value;
        }
        return $result;
    }

    function odds(){
        return $this->filter(
            function($n){return $n % 2 == 1;}
        );
    }

    function evens(){
        return $this->filter(
            function($n){return $n % 2 == 0;}
        );
    }
	
    function less_than($v){
        return $this->filter(
            function($x)use($v){return $x < $v;}
        );
    }

    function less_equal($v){
        return $this->filter(
            function($x)use($v){return $x <= $v;}
        );
    }

	
    function greater_than($v){
        return $this->filter(
            function($x)use($v){return $x > $v;}
        );
    }

    function greater_equal($v){
        return $this->filter(
            function($x)use($v){return $x >= $v;}
        );
    }

	
    function equal_to($v){
        return $this->filter(
            function($x)use($v){return $x == $v;}
        );
    }
	
    function in_interval($a, $b){
        return $this->filter(
            function($x)use($a, $b){return ($x >= $a) && ($x <= $b);}
        );
    }

    function outside_interval($a, $b){
        return $this->filter(
            function($x)use($a, $b){return ($x < $a) || ($x > $b);}
        );
    }


    function vowels(){
        return $this->filter(
            function($chr){return stripos("aeiou", strtolower($chr)) !== FALSE;}
        );
    }

    function consonants(){
        return $this->filter(
            function($chr){return stripos("aeiou", strtolower($chr)) === FALSE;}
        );

    
    }


    static function with($generator){
        return new self($generator);
    }

}

function from_array($arr){
    return LazyFluentFunctional::with(
        function() use($arr){
                foreach($arr as $key => $value){
                    if(yield $key => $value) return;
                }
            }
    );
}

function from_range($n1, $n2){
    return LazyFluentFunctional::with(
        function()use($n1, $n2){
                for($i = $n1; $i <= $n2; ++$i)
                    if(yield $i - $n1 => $i)
                        return;
            }
    );
}

function naturals(){
    return LazyFluentFunctional::with(
        function(){
                $n = 0;
                while(true)
                    if(yield $n++) return;
            }
    );
}

function from_file(string $fileName){
    return LazyFluentFunctional::with(
        function()use($fileName){
            if (!$fileHandle = fopen($fileName, 'r')) 
                return;
            $lineNumber = 0;
            while (false!== ($line = fgets($fileHandle))) 
                if(yield $lineNumber++ => $line)break;
            fclose($fileHandle);
        }
    );
}

function from_csv_file(string $fileName){
    return LazyFluentFunctional::with(        
        function()use($fileName){
            if (($handle = @fopen($fileName, "r")) !== FALSE) {
                $rowNo = 0;
                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                    if(yield ($rowNo++) => $data) break;
                }
                fclose($handle);
            }
        });
}

function from_string(string $str){
    return LazyFluentFunctional::with(        
        function()use($str){
            $len = strlen($str);
            for($idx = 0; $idx < $len; $idx++)
                if(yield $idx => $str[$idx]) return;
        });
}

function from_generator($generator){
    return LazyFluentFunctional::with($generator);
}
