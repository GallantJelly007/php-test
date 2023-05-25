<?php

require_once ROOT.'/core/Model.php';

class UserJournal extends Model {
    protected static $table="user_journal";
    protected static $primary="id";
    
    public $id;
    public $userId;
    public $os;
    public $ip;
    public $dateEntry;
}