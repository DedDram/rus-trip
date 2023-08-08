<?php
namespace Services;

use Exceptions\DbException;
use PDO;

class Db
{
    /** @var PDO */
    private PDO $pdo;
    private static Db $instance;

    /**
     * @throws DbException
     */
    private function __construct()
    {
        $dbOptions = (require __DIR__ . '/../settings.php')['db'];
        try {
            $this->pdo = new PDO(
                'mysql:host=' . $dbOptions['host'] . ';dbname=' . $dbOptions['dbname'],
                $dbOptions['user'],
                $dbOptions['password']
            );
            $this->pdo->exec('SET NAMES UTF8');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new DbException('Ошибка при подключении к базе данных: ' . $e->getMessage());
        }
    }

    public function query(string $sql, $params = [], string $className = 'stdClass'): ?array
    {
        $this->pdo->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );
       // if($_SERVER['REMOTE_ADDR']=='37.110.34.79'){
       //     echo "<pre>$sql</pre>";
       // }
       // $start_time = microtime(true);
        $sth = $this->pdo->prepare($sql);
        $result = $sth->execute($params);
      //  $query_time = (microtime(true)-$start_time);
       // echo "<pre>$query_time</pre>";
        if (false === $result) {
            return null;
        }

        return $sth->fetchAll(PDO::FETCH_CLASS, $className);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getLastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function quote(string $value): string
    {
        return $this->pdo->quote($value);
    }
}