<?php

class Database
{
    use Singleton;
    
    private $pdo;
    private $count = 0;
    private $debug = 0;

    public function __construct()
    {
        $settings = config::getSection('database');
        try 
        {
            $this->pdo =
                new PDO("mysql:unix_socket=/tmp/mysql.sock;dbname={$settings['db_database']}",
                    $settings['db_login'], $settings['db_password'], array(PDO::ATTR_PERSISTENT => true));
        } catch (Exception $ex) {
            throw new databaseException($ex->getMessage(), 2502, null);
        }
    }
    
    public function __destruct() 
    {
        unset($this->pdo);
    }

    public function quote($value)
    {
        return $this->pdo->quote($value);
    }
    
    public function query($query, $params = array())
    {
        $res = $this->pdo->prepare($query);

        if($this->debug) { $timeStart = microtime(true); }
        $res->execute($params);
        if($this->debug) { misc::writeDebug(sprintf("Query: {$query} @ %0.8f", microtime(true) - $timeStart)); }
        
        /* Error thrower */
        if($res->errorCode() != "0000")
        {
            $err = $res->errorInfo();
            throw new databaseException($err[2], 2502);
        }

        $this->count ++;
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function query_single_col($query, $params = array())
    {
        $res = $this->pdo->prepare($query);

        if($this->debug) { $timeStart = microtime(true); }
        $res->execute($params);
        if($this->debug) { misc::writeDebug(sprintf("Query: {$query} @ %0.8f", microtime(true) - $timeStart)); }
        
        /* Error thrower */
        if($res->errorCode() != "0000")
        {
            $err = $res->errorInfo();
            throw new databaseException($err[2], 2502);
        }

        if ($res->rowCount() > 0)
        {
            $val = $res->fetch(PDO::FETCH_NUM);
            $this->count ++;
            return $val[0];
        }
        else
        {
            return null;
        }
    }

    public function query_single_row($query, $params = array())
    {
        $res = $this->pdo->prepare($query);
        
        if($this->debug) { $timeStart = microtime(true); }
        $res->execute($params);
        if($this->debug) { misc::writeDebug(sprintf("Query: {$query} @ %0.8f", microtime(true) - $timeStart)); }
        
        /* Error thrower */
        if($res->errorCode() != "0000")
        {
            $err = $res->errorInfo();
            throw new databaseException($err[2], 2502);
        }

        if ($res->rowCount() > 0)
        {
            $this->count ++;
            return $res->fetch(PDO::FETCH_ASSOC);
        }
        else
        {
            return null;
        }
    }

    public function query_update($query, $params = array())
    {
        if ($this->pdo == null)
        {
            self::connect();
        }
        
        $res = $this->pdo->prepare($query);

        if($this->debug) { $timeStart = microtime(true); }
        $res->execute($params);
        if($this->debug) { misc::writeDebug(sprintf("Query: {$query} @ %0.8f", microtime(true) - $timeStart)); }
        
        /* Error thrower */
        if($res->errorCode() != "0000")
        {
            $err = $res->errorInfo();
            throw new databaseException($err[2], 2502);
        }
        
        $this->count ++;
        return $res->rowCount();
    }

    public function lastError()
    {
        $arr = $this->pdo->errorInfo();
        return $arr[0];
    }
    
    public function lastInsertId($name = null)
    {
        return $this->pdo->lastInsertId($name);
    }
    
    public function __toString()
    {
        return $this->count;
    }

}
