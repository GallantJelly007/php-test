<?php

require_once ROOT.'/core/Model.php';

class User extends Model {
    protected static $table="users";
    protected static $primary="id";
    
    public $id;
    public $email;
    public $pass;
    public $keyToken;
    public $keyRtoken;
    public $name;
    public $surname;
    public $avatar;
    public $dateReg;
    public $role;
}
