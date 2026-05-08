<?php
require_once "DBcontrollers.php";
// تصحيح: يجب استدعاء ملفات الـ Controller والـ Model أولاً
require_once "contractController.php";
require_once "../Models/contract.php";

$db = DBcontrollers::getInstance();

$proposal_id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if ($proposal_id && $status) {
    // 1. تحديث حالة العرض (Proposal)
    $update_query = "UPDATE proposal SET status = '$status' WHERE proposal_id = '$proposal_id'";
    $db->insertquery($update_query);

    if ($status == 'Accepted') {
        // 1. استدعاء الملفات المطلوبة أولاً
        require_once "../Models/contract.php";
        require_once "contractController.php";

        // 2. جلب بيانات العرض (Proposal)
        $proposal_data = $db->Select_query("SELECT * FROM proposal WHERE proposal_id = '$proposal_id'");
        $project_id = $proposal_data[0]['project_id'];
        $freelancer_id = $proposal_data[0]['freelancer_id'];

        // 3. تحديث حالة المشروع
        $db->insertquery("UPDATE projects SET status = 'In Progress' WHERE project_id = '$project_id'");

        // 4. إنشاء كائن العقد وتعبئة بياناته (تأكد من حرف i في limits)
        $contract = new contract();
        $contract->project_id = $project_id;
        $contract->freelancer_id = $freelancer_id;
        $contract->deadline = $proposal_data[0]['Delivery_time']; 
        $contract->revision_limits = 3; 
        $contract->revision_used = 0;
        $contract->create_at = date("Y-m-d H:i:s");

        // 5. حفظ العقد في الداتابيز واستلام الـ ID الجديد
        $contract_manager = new createcontract();
        $new_id = $contract_manager->create_contract($contract);

        // 6. التحويل لصفحة التفاصيل بالـ ID الجديد
        if ($new_id) {
            header("Location: ../views/contract_details/contract_details.php?contract_id=$new_id");
            exit();
        }
    }
    

    header("Location: ../views/proposal_request/proposal_request.php?msg=StatusUpdated");
    exit();
}
?>