<?php

require_once __DIR__ . "/DBcontrollers.php";

/**
 * FreelancerProfileController
 * Handles Section A functions for the Freelance Gig Platform
 */
class FreelancerProfileController {

    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    // ══════════════════════════════════════════════════════════════
    // FUNCTION 1 — Credential Verification Workflow
    // ══════════════════════════════════════════════════════════════

    /**
     * Submit documents for verification (freelancer action)
     */
    public function submitVerificationDocument($freelancer_id, $document_type, $document_path) {
        $freelancer_id  = $this->db->connection->real_escape_string($freelancer_id);
        $document_type  = $this->db->connection->real_escape_string($document_type);
        $document_path  = $this->db->connection->real_escape_string($document_path);

        // 1. تسجيل المستند في جدول المستندات
        $insertDoc = $this->db->insertquery(
            "INSERT INTO freelancer_documents (freelancer_id, document_type, document_path, status, uploaded_at)
             VALUES ('$freelancer_id', '$document_type', '$document_path', 'pending', NOW())"
        );

        // 2. تحديث حالة البروفايل في الجدول الجديد
        if ($insertDoc) {
            $this->db->insertquery(
                "UPDATE freelancer_profiles 
                 SET verification_status = 'documents_uploaded' 
                 WHERE user_id = '$freelancer_id'"
            );
        }

        return $insertDoc;
    }

    /**
     * Admin action to approve or reject a document.
     */
    public function adminReviewDocument($document_id, $freelancer_id, $status, $note = '') {
        $document_id   = $this->db->connection->real_escape_string($document_id);
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
        $status        = $this->db->connection->real_escape_string($status);
        $note          = $this->db->connection->real_escape_string($note);
        $admin_id      = $_SESSION['userid'] ?? 0;

        // تحديث جدول المستندات
        $updateDoc = $this->db->insertquery("
            UPDATE freelancer_documents 
            SET status = '$status', 
                reviewed_by = '$admin_id', 
                review_note = '$note', 
                reviewed_at = NOW() 
            WHERE document_id = '$document_id'
        ");

        // إذا تمت الموافقة، نحدث الحالة في جدول البروفايل الجديد
        if ($updateDoc && $status === 'approved') {
            return $this->db->insertquery("
                UPDATE freelancer_profiles 
                SET verification_status = 'verified' 
                WHERE user_id = '$freelancer_id'
            ");
        }

        return $updateDoc;
    }

    /**
     * Get verification status and documents for a freelancer
     */
    public function getVerificationStatus($freelancer_id) {
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
        
        // جلب الحالة من الجدول الجديد freelancer_profiles
        $res = $this->db->Select_query("SELECT verification_status FROM freelancer_profiles WHERE user_id = '$freelancer_id'");
        $status = !empty($res) ? $res[0]['verification_status'] : 'pending';
        
        $docs = $this->db->Select_query("SELECT * FROM freelancer_documents WHERE freelancer_id = '$freelancer_id'");
        
        return ['status' => $status, 'documents' => $docs];
    }

    // ══════════════════════════════════════════════════════════════
    // FUNCTION 2 — Privacy Settings
    // ══════════════════════════════════════════════════════════════

    /**
     * Update profile privacy visibility settings
     */
    public function updatePrivacySettings($freelancer_id, $show_earnings, $show_clients, $show_contact) {
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
        
        return $this->db->insertquery("
            UPDATE freelancer_profiles 
            SET show_earnings = '$show_earnings', 
                show_client_names = '$show_clients', 
                show_contact = '$show_contact' 
            WHERE user_id = '$freelancer_id'
        ");
    }

    /**
     * Get public profile data with privacy filters applied
     */
    public function getPublicProfile($freelancer_id, $viewer_id = null) {
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
        
        // جلب البيانات بدمج جدول اليوزر مع جدول البروفايل الجديد
        $data = $this->db->Select_query("
            SELECT u.name, u.email, p.* FROM user u 
            JOIN freelancer_profiles p ON u.user_id = p.user_id 
            WHERE u.user_id = '$freelancer_id'
        ");

        if (empty($data)) return null;
        $profile = $data[0];

        // تطبيق منطق الخصوصية (Privacy Logic)
        if (!$profile['show_earnings'] && $viewer_id != $freelancer_id) {
            $profile['total_earnings'] = "Hidden"; 
        }

        return $profile;
    }
    public function addFreelancerSkills($freelancer_id, $skills_array) {
    $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
    foreach ($skills_array as $skill_id) {
        $skill_id = $this->db->connection->real_escape_string($skill_id);
        $this->db->insertquery("INSERT INTO freelancer_skills (freelancer_id, skill_id) VALUES ('$freelancer_id', '$skill_id')");
    }
}
}