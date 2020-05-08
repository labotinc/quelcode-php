<?php

declare(strict_types=1);

$limit = $_GET['target'];

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
    echo '<pre>';
    print_r($nums);
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

  /*
   * 2 => [[1,1], [1,0], [0,1], [0,0]]
   */
  private static function lengthToBitPattern2DArray(int $length): array
  {
    $numOfFullBit = pow(2, $length) - 1; // 11111
    $_2DArray = [];
    for ($i = 0; $i <= $numOfFullBit; $i++) {
      $bitmap = decbin($numOfFullBit - $i);
      $currentLen = strlen($bitmap);
      $pad = self::get0Pad($currentLen, $length);
      $bitmap = $pad . $bitmap;
      $_2DArray[] = str_split($bitmap);
    }
    return $_2DArray;
  }
}
