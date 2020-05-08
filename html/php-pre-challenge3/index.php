<?php

declare(strict_types=1);

$limit = $_GET['target'] ?? '';

$dsn = 'mysql:dbname=test;host=mysql';
$dbuser = 'test';
$dbpassword = 'test';

PhpPreChallenge3::run($limit, $dsn, $dbuser, $dbpassword);

class PhpPreChallenge3
{
  public static function run(
    string $limitStr,
    string $dsn,
    string $dbuser,
    string $dbpassword
  ) {
    try {
      self::runCore($limitStr, $dsn, $dbuser, $dbpassword);
    } catch (Exception $e) {
      http_response_code(400);
      echo json_encode($e->getMessage());
    }
    exit();
  }

  /**
   * 例外投げます
   */
  private static function runCore(
    string $limitStr,
    string $dsn,
    string $dbuser,
    string $dbpassword
  ) {
    $limit = self::getLimitInt($limitStr);
    $nums = self::getNumsFromDb($limit, $dsn, $dbuser, $dbpassword);
    $length = count($nums);
    $bitPatterns = self::lengthToBitPatterns($length);
    echo '<pre>';
    print_r($nums);

    $num = 10;
    echo self::numUnsignedToBittPatternStr($num, 1) . '<br>';
    echo self::numUnsignedToBittPatternStr($num, 2) . '<br>';
    echo self::numUnsignedToBittPatternStr($num, 3) . '<br>';
    echo self::numUnsignedToBittPatternStr($num, 4) . '<br>';
    echo self::numUnsignedToBittPatternStr($num, 5) . '<br>';
    echo self::numUnsignedToBittPatternStr($num, 6) . '<br>';
    echo self::numUnsignedToBittPatternStr($num, 7) . '<br>';
  }

  /**
   * 入力文字列が1以上の整数でなければ例外を投げる。
   * 1以上の整数なら整数型にして返却する。
   */
  private static function getLimitInt(string $limitStr): int
  {
    $errMsg = 'invalid limit : ' . $limitStr;
    if (false === ctype_digit($limitStr)) {
      // 整数ではない、または負の値である
      throw new Exception($errMsg);
    }
    $limit = intval($limitStr);
    if ($limit < 1) {
      // 1未満である
      throw new Exception($errMsg);
    }
    // ガード突破
    return $limit;
  }

  /**
   * dbから値を整数型の昇順で取得する。
   * limitが4なら5以上の値は除外する
   */
  private static function getNumsFromDb(
    int $limit,
    string $dsn,
    string $dbuser,
    string $dbpassword
  ): array {
    $nums = [];
    try {
      $pdo = new Pdo($dsn, $dbuser, $dbpassword);
      $sql = 'select value from prechallenge3 ';
      $sql .= 'where value <= :value ';
      $sql .= 'order by value asc';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':value', $limit);
      $stmt->execute();
      $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($records as $record) {
        // 整数型にする
        $nums[] = intval($record['value']);
      }
    } catch (PDOException $e) {
      throw $e;
    }
    return $nums;
  }

  /**
   * 上位bitの'0'pad取得
   */
  private static function get0Pad(int $currentLen, int $maxLen): string
  {
    $cnt = $maxLen - $currentLen;
    $pad = '';
    for ($i = 0; $i < $cnt; $i++) {
      $pad .= '0';
    }
    return $pad;
  }

  // 0または正の整数を指定のbit長の2進数文字列にする
  private static function numUnsignedToBittPatternStr(int $num, int $length): string
  {
    if ($num < 0 || $length <= 0) {
      throw new Exception('numToBittPatternStr() invalid num or length : ' . $num . ', ' . $length);
    }
    $binStr = decbin($num);
    $binLen = strlen($binStr);
    if ($binLen < $length) {
      // lengthに足りない
      $cnt = $length - $binLen;
      for ($i = 0; $i < $cnt; $i++) {
        $binStr = '0' . $binStr;
      }
    } else if ($length < $binLen) {
      // lengthをはみ出す
      $pos = $binLen - $length;
      $binStr = substr($binStr, $pos);
    } else {
      // lengthと同じ長さ
      /* DO NOTHING */
    }
    return $binStr;
  }

  /*
   * 2 => [[1,1], [1,0], [0,1], [0,0]]
   */
  private static function lengthToBitPatterns(int $length): array
  {
    $numOfFullBit = pow(2, $length) - 1; // 11111
    $bitPatterns = [];
    for ($i = 0; $i <= $numOfFullBit; $i++) {
      $bitmap = decbin($numOfFullBit - $i);
      $currentLen = strlen($bitmap);
      $pad = self::get0Pad($currentLen, $length);
      $bitmap = $pad . $bitmap;
      $bitPattern = str_split($bitmap);
      // 要素を整数型にする
      array_walk($bitPattern, function (&$value) {
        $value = intval($value);
      });
      $bitPatterns[] = $bitPattern;
    }
    return $bitPatterns;
  }
}
