<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController
{
    public static function index()
    {
        Auth::requireLogin();

        $metrics = Dashboard::metrics();

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
