<?php

declare(strict_types=1);

$limit = $_GET['target'];

$dsn = 'mysql:dbname=test;host=mysql';
$dbuser = 'test';
$dbpassword = 'test';

PhpPreChallenge3::run($limit);

class PhpPreChallenge3
{
  public static function run(string $limitStr)
  {
    try {
      $limit = self::getLimitInt($limitStr);
    } catch (Exception $e) {
      echo json_encode($e->getMessage());
      exit();
    }

    echo $limit;
  }

  /**
   * 入力文字列が1以上の整数でなければ例外を投げる。
   * 1以上の整数なら整数型にして返却する。
   */
  public static function getLimitInt(string $limitStr): int
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
}
