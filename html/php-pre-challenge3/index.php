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
  public static function getLimitInt(string $limitStr): int
  {
    if (true) {
      throw new Exception('invalid limit : ' . $limitStr);
    }
    return 0;
  }
}
