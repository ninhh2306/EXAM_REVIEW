<?php

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard'
        ];

        $this->viewAdmin('dashboard/index', $data);
    }
}