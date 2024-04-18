<?php
namespace App\Table;
use GemLibrary\Database\PdoQuery;

class UserTable extends PdoQuery
{

    public int $id;
    public string $email;
    public string $password;
    public function __construct()
    {
        parent::__construct();
    }

    public function getTable():string{
        return 'users';
    }

    public function insert():bool|int{
        $query = "INSERT INTO {$this->getTable()}  (email,password) VALUES (:email,:passwd) ";
        $arrayBind = [':email' => $this->email , ':passwd'=> $this->password];
        return $this->insertQuery($query,$arrayBind);
    }

    public function selectByEmail():bool{
        $query = "SELECT * FROM {$this->getTable()} WHERE email = :email LIMIT 1";
        $arrayBind = [':email' => $this->email];

        $result = $this->selectQuery($query,$arrayBind);
        if(!$result || count($result) !== 1)
        {
            return false;
        }
        $result = $result[0];
        $this->id = $result['id'];
        $this->email = $result['email'];
        $this->password = $result['password'];
        return true;
    }
}