<?php

require_once ROOT.'/core/Model.php';

class Obj extends Model{
    protected static $table="objects";
    protected static $primary="id";
    
    public $id;
    public $userId;
    public $parentId;
    public $title;
    public $description;
}