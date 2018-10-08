<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 03.10.2018
 * Time: 22:11
 */

class Reg_Auth{

    private $login;
    private $pass;

    function __construct($login,$pass)
    {
        $this->login = htmlspecialchars($login);
        $this->pass = htmlspecialchars($pass);
    }
    function login_valid(){
        if (filter_var($this->login,FILTER_VALIDATE_EMAIL)
            || preg_match("/([a-zA-Z0-9\.-_])*/",$this->login)
            && strlen($this->login) >= 2
        ){
            return true;
        }
        return false;
    }
    function password_valid(){
        if (strlen($this->pass) >= 2 && preg_match("/([a-zA-Z0-9])*/",$this->pass)){
            return true;
        }
        return false;
    }
    function get_login(){
        return $this->login;
    }
}