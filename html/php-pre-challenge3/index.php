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
  ): void {
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
  ): void {
    $limit = self::getLimitInt($limitStr);
    $nums = self::getNumsFromDb($limit, $dsn, $dbuser, $dbpassword);
    $length = count($nums);
    $numOfFullBit = pow(2, $length) - 1;

    $patterns = [];
    for ($i = 1; $i <= $numOfFullBit; $i++) {
      $binStr = self::numUnsignedToBinStr($i, $length);
      $sum = self::arraySumAtBinStr($nums, $binStr);
      if ($sum === $limit) {
        /* パターン発見 */
        $binArray = str_split($binStr);
        $pattern = [];
        foreach ($binArray as $idx => $bit) {
          if ($bit) {
            $pattern[] = $nums[$idx];
          }
        }
        $patterns[] = $pattern;
      }
    }
    // 見栄えをよくする
    $patterns = array_reverse($patterns);
    echo json_encode($patterns);
  }

  /**
   * 入力文字列が1以上の整数でなければ例外を投げる
   * 1以上の整数なら整数型にして返却する
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
   * dbから値を整数型の昇順で取得する
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
   * 0または正の整数を指定のbit長の2進数文字列にする
   * 9, 3 => 001 
   * 9, 4 => 1001
   * 9, 5 => 01001
   */
  private static function numUnsignedToBinStr(int $num, int $length): string
  {
    if ($num < 0 || $length <= 0) {
      throw new Exception('numUnsignedToBinStr() invalid num or length : ' . $num . ', ' . $length);
    }
    $binStr = decbin($num);
    $binLen = strlen($binStr);
    if ($binLen < $length) {
      /* lengthに足りない */
      $cnt = $length - $binLen;
      // パディング
      for ($i = 0; $i < $cnt; $i++) {
        $binStr = '0' . $binStr;
      }
    } else if ($length < $binLen) {
      /* lengthをはみ出す */
      $pos = $binLen - $length;
      // スライス
      $binStr = substr($binStr, $pos);
    } else {
      // lengthと同じ長さ
      /* DO NOTHING */
    }
    return $binStr;
  }

  /**
   * [1,2,3,4], 1111 => 10
   * [1,2,3,4], 1101 => 7
   */
  private static function arraySumAtBinStr(array $a, string $binStr): int
  {
    $binArray = str_split($binStr);
    $sum = 0;
    foreach ($binArray as $idx => $bit) {
      if ($bit) {
        $sum += $a[$idx];
      }
    }
    return $sum;
  }
}
