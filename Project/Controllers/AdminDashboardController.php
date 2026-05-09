<?php
// بما أن كل الملفات في نفس الفولدر (controllers)
require_once "AdminActions.php"; 
require_once "ProjectActions.php";

class AdminDashboardController {
    public function showDashboard() {
        $adminModel = new AdminModel();
        $projectModel = new ProjectModel();
        $stats = $adminModel->getSystemStats(); 
        $projects = $projectModel->getAllProjects(); 

    
        include '../admin-dashboard.php'; 
    }
}

// تشغيل الفانكشن
$controller = new AdminDashboardController();
$controller->showDashboard();