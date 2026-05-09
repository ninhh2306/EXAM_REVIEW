<?php

require_once ROOT . "/app/core/Controller.php";

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
    }
}