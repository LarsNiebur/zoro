<?php
namespace App\Model;
use App\Table\UserTable;
use GemLibrary\Helper\CryptHelper;

class UserModel extends UserTable
{
    public function __construct()
    {
        parent::__construct();
    }

    public function register():UserModel|false
    {
        $this->password = CryptHelper::hashPassword($this->password);
        $id = $this->insert();
        if($id === false || $id < 1)
        {
            return false;
        }
        if(is_int($id))
        {
            $this->id = $id;
        }
        return $this;
    }

    /**
     * @param string $password
     * @return false|UserModel
     * in case of success return JWT Token
     */
    public function loginByEmail(string $password):false|UserModel{
        if(!$this->selectByEmail())
        {
            return false;
        }
        if(!CryptHelper::passwordVerify($password,$this->password))
        {
            return false;
        }
        return $this;
    }
}