<?php

$array = explode(',', $_GET['array']);

// 修正はここから
$amount = count($array);
$start = -1;

// don't need to declare $amount as variable as this if statement sets it automatically
// $amount= count($array); FOLLOWING IF STATEMENT IS AUTOMATIICALLY DECLARED WHEN USING IF STATEMENT

while( $start < $amount ){
    ++$start;
    --$amount;
    // start begins the sorting from the first array element
    // amount begins the sorting from the last array element

    for( $i = 0 ; $i < $amount; ++$i){
        if( $array[ $i ] > $array[ $i + 1 ]){
            $tmp = $array[ $i ];
            $array[ $i ] = $array[ $i + 1 ];
            $array[ $i + 1 ] = $tmp;
        }
    }
    for( $i = $amount; --$i >= $start;){
        if( $array[ $i ] > $array[ $i + 1 ]){
            $tmp = $array[ $i ];
            $array[ $i ] = $array[ $i + 1 ];
            $array[ $i + 1 ] = $tmp;
        }
    }
}

// 修正はここまで
echo "<pre>";
print_r($array);
echo "</pre>";
