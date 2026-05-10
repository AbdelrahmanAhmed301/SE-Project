<?php
session_start();
require_once "../../Controllers/contractController.php";
$db = DBcontrollers::getInstance();
$manager = new createcontract();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $contract_id = $_POST['contract_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
    // هنجيب رقم المشروع المرتبط بالعقد الأول أو نحدث العقد نفسه
    $db->insertquery("UPDATE projects SET status = 'Completed' WHERE project_id = (SELECT project_id FROM contract WHERE contract_id = '$contract_id')");
    header("Location: contract_details.php?contract_id=$contract_id&status=success");
}
    elseif ($action == 'revise') {
    // استعلام بيزود القيمة الموجودة في الداتابيز بـ 1 مباشرة
    $query = "UPDATE contract SET revision_used = revision_used + 1 WHERE contract_id = '$contract_id'";
    

    $db->insertquery($query); 

    header("Location: contract_details.php?contract_id=$contract_id&status=revision_requested");
}
    exit();
}