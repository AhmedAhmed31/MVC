<?php

class  controller
{


    public function model($model)
    {
        require_once APP_PATH.DS.'models' . DS . $model . '.php';
        return new $model;

    }

    public function view($view, $data = [])
    {
        require_once APP_PATH . DS . 'view' . DS . $view . '.php';
    }
}