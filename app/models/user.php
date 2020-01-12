<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class user extends Eloquent
{

    public $name;
    protected $fillable = ['username', 'email'];
}