<?php

class home extends controller

{
    protected $user;

    public function __construct()
    {
        $this->user = $this->model('user');
    }

    public function index($name = '')
    {
        $user = $this->user;
        $user->$name = $name;
        $this->view('home/index', ['name' => $user->$name]);
    }

    public function create($username = '', $email = '')
    {
        User::create([
            'name' => $username,
            'email' => $email
        ]);
    }


}