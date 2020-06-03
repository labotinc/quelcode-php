<?php

$array = explode(',', $_GET['array']);

// 修正はここから
$length = count($array);
$比較 = 0;

echo "<h1>Bubble Sort Algorithm</h2>";

for ( $i = 0; $i < $length; $i++ ) {
    for( $j = 0; $j < $length - 1; $j++ ){
        $比較++;
        if( $array[ $j ] > $array[ $j + 1 ] ) {
            $tmp = $array[ $j + 1 ];
            $array[ $j + 1 ] = $array[ $j ];
            $array[ $j ] = $tmp;
        }
    }
}
// 修正はここまで

echo "<pre>";
echo '<h4>' . $比較. ' 比較回数 / Comparisons </h4>';
print_r($array);
echo "</pre>";



echo "<h1>Bidirectional Bubble Sort Algorithm</h2>";


$array = explode(',', $_GET['array']);

// 修正はここから
$comparisons = 0;
$start = -1;

if( !$amount = $length ){
    return $array;
}
while( $start < $amount ){
    ++$start;
    --$amount;
    
    for( $i = 0 ; $i < $amount; ++$i){
        if( $array[ $i ] > $array[ $i + 1 ]){
            $tmp = $array[ $i ];
            $array[ $i ] = $array[ $i + 1 ];
            $array[ $i + 1 ] = $tmp;
        }
        $comparisons++;
    }
    for( $i = $amount; --$i >= $start;){
        if( $array[ $i ] > $array[ $i + 1 ]){
            $tmp = $array[ $i ];
            $array[ $i ] = $array[ $i + 1 ];
            $array[ $i + 1 ] = $tmp;
        }
        $comparisons++;
    }
}

// 修正はここまで

echo "<pre>";
echo '<strong>並べ替え後 / After sorting</strong><br>';
echo '<h4>' . $comparisons . ' 比較回数 / Comparisons </h4>';
print_r($array);
echo "</pre>";