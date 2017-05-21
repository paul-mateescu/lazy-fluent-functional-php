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
                foreach($generator() as $key => $value){
                    if($noOfRowsToDrop-- > 0) continue;
                    if(yield $key => $value) return;
                }
            };
        return $this;
    }

    public function drop_while(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $predicate){
                $toDrop = true;
                foreach($generator() as $key => $value){
                    if($toDrop){
                        $toDrop = $predicate($value, $key);
                        if($toDrop) continue;
                    }
                    if(yield $key => $value) return;
                }
            };
        return $this;


    }

    public function drop_until(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $predicate){
                $toDrop = true;
                foreach($generator() as $key => $value){
                    if($toDrop){
                        $toDrop = ! $predicate($value, $key);
                        if($toDrop) continue;
                    }
                    if(yield $key => $value) return;
                }
            };
        return $this;
    }

    public function map(callable $transform){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $transform){
                foreach($generator() as $key => $value)
                    if(yield $key => $transform($value)) return;
                };
        return $this;
    }

    public function take($noOfRowsToTake){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $noOfRowsToTake){
                foreach($generator() as $key => $value){
                    if($noOfRowsToTake-- <= 0) return;
                    if(yield $key => $value) return;
                    }
                };
        return $this;
    }

    public function take_while(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach($generator() as $key => $value){
                    if(! $predicate($value, $key)) return;
                    if(yield $key => $value) return;
                    }
                };
        return $this;
    }

    public function take_until(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach($generator() as $key => $value){
                    if($predicate($value, $key)) return;
                    if(yield $key => $value) return;
                    }
                };
        return $this;
    }

    public function filter(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach($generator() as $key => $value){
                    if($predicate($value, $key)) 
                        if(yield $key => $value) return;
                }
            };
        return $this;
    }

    public function exclude(callable $predicate){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $predicate){
                foreach($generator() as $key => $value){
                    if(!$predicate($value, $key)) 
                        if(yield $key => $value) return;
                }
            };
        return $this;
    }

    public function column($column_key){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $column_key){
                    foreach($generator() as $key => $value)
                        if(yield $key => $value[$column_key]) return;
                    };
        return $this;
    }

    function columns(...$column_keys){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $column_keys){
                foreach($generator() as $key => $value){
                    $arr = [];
                    foreach($column_keys as $column_key) 
                        // ATENTIE: 
                        // era:
                        // $arr []= $value[$column_key];
                        $arr [$column_key]= $value[$column_key];
                    if(yield $key => $arr) return;
                    }
                };
        return $this;
    }

    function delete_column($column_key){
        $generator = $this->generator;
        $this->generator = 
            function ()use($generator, $column_key){
                    foreach($generator() as $key => $value){
                        unset($value[$column_key]);
                        if(yield $key => $value) return;
                        }
                    };
        return $this;
    }

    function reindex(){
        $generator = $this->generator;
        $this->generator = 
                function() use ($generator){
                    $i = 0;
                    foreach($generator() as $key => $value)
                        if(yield $i++  => $value) return;
                    }
                ;
        return $this;
    }

    function key_from_column($column_key){
        $generator = $this->generator;
        $this->generator = 
            function() use($generator, $column_key){
                    foreach($generator() as $value)
                        if(yield $value[$column_key] => $value) return;
                    };
        return $this;
    }

    function sort_asc_on_key(){
        $generator = $this->generator;
        $this->generator = 
            function()use($generator){
                    $arr = [];
                    foreach($generator() as $key => $value)
                        $arr[$key] = $value;
                    ksort($arr);
                    foreach($arr as $key => $elem)
                        if(yield $key => $elem) return;
                    };
        return $this;
    }

    function sort_desc_on_key(){
        $generator = $this->generator;
        $this->generator = 
            function()use($generator){
                    $arr = [];
                    foreach($generator() as $key => $value)
                        $arr[$key] = $value;
                    krsort($arr);
                    foreach($arr as $key => $elem)
                        if(yield $key => $elem) return;
                    };
        return $this;
    }

    function sort_desc_on_column($column_key){
        $generator = $this->generator;
        $this->generator = 
            function()use($generator, $column_key){
                    $arr = [];
                    foreach($generator() as $key => $value)
                        $arr[$value[$column_key]] = [$key, $value];
                    krsort($arr);
                    foreach($arr as $elem)
                        if(yield $elem[0] => $elem[1]) return;
                    };
        return $this;
    }

    function group_on_column($column_key){
        $generator = $this->generator;
        $this->generator = 
             function() use ($generator, $column_key){
                    $arr = [];
                    foreach($generator() as $value)
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
        $generator = $this->generator;
        $this->generator = 
            function()use($generator) {
                    $arr = [];
                    foreach($generator() as $key => $value)
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
        return iterator_to_array(($this->generator)());
    }

    function reduce($reduce_function){
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
