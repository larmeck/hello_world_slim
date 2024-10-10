<?php

namespace App\Models;

use \PDO;

class DB
{
    private $host = '127.0.0.1';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'customers_api';

    public function connect()
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}
