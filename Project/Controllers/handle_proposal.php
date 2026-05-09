<?php
// handle_proposal.php

require_once "DBcontrollers.php";
require_once "contractController.php";
require_once "../Models/contract.php";

$db = DBcontrollers::getInstance();

// استقبال البيانات من الرابط (URL)
$proposal_id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if ($proposal_id && $status) {

    // 1. تحديث حالة البروبوزال (Accepted / Rejected)
    $update_query = "UPDATE proposal SET status = '$status' WHERE proposal_id = '$proposal_id'";
    $db->insertquery($update_query);

    // 2. في حالة الموافقة: إنشاء العقد وتحديث حالة المشروع
    if ($status == 'Accepted') {
    
        // جلب بيانات البروبوزال عشان نعرف المشروع والفريلانسر والمدة المطلوبة
        $proposal_data = $db->Select_query("SELECT * FROM proposal WHERE proposal_id = '$proposal_id'");
        
        if ($proposal_data) {
            $project_id = $proposal_data[0]['project_id'];
            $freelancer_id = $proposal_data[0]['freelancer_id'];
            $raw_delivery_time = $proposal_data[0]['Delivery_time']; // القيمة المخزنة (مثل +1 month)

            // تحديث حالة المشروع ليصبح "قيد التنفيذ"
            $db->insertquery("UPDATE projects SET status = 'In Progress' WHERE project_id = '$project_id'");

            // إنشاء كائن العقد (Contract Object)
            $contract = new contract();
            $contract->project_id = $project_id;
            $contract->freelancer_id = $freelancer_id;

            /**
             * حل مشكلة الـ Deadline:
             * نستخدم strtotime لتحويل النص (مثل +2 months) لتاريخ حقيقي يبدأ من اليوم.
             * الـ '_' لازم تتحول لمسافة لو كانت مخزنة بـ underscore.
             */
            $clean_time = str_replace('_', ' ', $raw_delivery_time);
            $contract->deadline = date('Y-m-d', strtotime($clean_time));
            
            // إعدادات العقد الافتراضية
            $contract->revision_limits = 3; 
            $contract->revision_used = 0;
            $contract->create_at = date("Y-m-d H:i:s");

            // حفظ العقد في قاعدة البيانات باستخدام الكنترولر
            $contract_manager = new createcontract();
            $new_id = $contract_manager->create_contract($contract);

            // لو العقد تم إنشاؤه بنجاح، حول الكلاينت لصفحة تفاصيل العقد
            if ($new_id) {
                header("Location: ../views/contract_details/contract_details.php?contract_id=$new_id");
                exit();
            }
        }
    }

    // في حالة الرفض أو حدوث خطأ، العودة لصفحة الطلبات
    header("Location: ../views/proposal_request/proposal_request.php?msg=StatusUpdated");
    exit();
} 
?>