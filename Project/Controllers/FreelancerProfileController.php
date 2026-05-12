<?php

require_once __DIR__ . "/DBcontrollers.php";


class FreelancerProfileController {

    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }
    public function someAction($userId) {
         $db = DBcontrollers::getInstance();
        if (user::isBanned($userId, $db)) {
                die ("Error: Your account is restricted.");
        exit();
    }
}
    public function submitVerificationDocument($freelancer_id, $document_type, $document_path) {
        $freelancer_id  = $this->db->connection->real_escape_string($freelancer_id);
        $document_type  = $this->db->connection->real_escape_string($document_type);
        $document_path  = $this->db->connection->real_escape_string($document_path);

        $insertDoc = $this->db->insertquery(
            "INSERT INTO freelancer_documents (freelancer_id, document_type, document_path, status, uploaded_at)
             VALUES ('$freelancer_id', '$document_type', '$document_path', 'pending', NOW())"
        );

  
        if ($insertDoc) {
            $this->db->insertquery(
                "UPDATE freelancer_profiles 
                 SET verification_status = 'documents_uploaded' 
                 WHERE user_id = '$freelancer_id'"
            );
        }

        return $insertDoc;
    }

   
    public function adminReviewDocument($document_id, $freelancer_id, $status, $note = '') {
        $document_id   = $this->db->connection->real_escape_string($document_id);
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
        $status        = $this->db->connection->real_escape_string($status);
        $note          = $this->db->connection->real_escape_string($note);
        $admin_id      = $_SESSION['userid'] ?? 0;

        $updateDoc = $this->db->insertquery("
            UPDATE freelancer_documents 
            SET status = '$status', 
                reviewed_by = '$admin_id', 
                review_note = '$note', 
                reviewed_at = NOW() 
            WHERE document_id = '$document_id'
        ");

        if ($updateDoc && $status === 'approved') {
            return $this->db->insertquery("
                UPDATE freelancer_profiles 
                SET verification_status = 'verified' 
                WHERE user_id = '$freelancer_id'
            ");
        }

        return $updateDoc;
    }

    public function getVerificationStatus($freelancer_id) {
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);

        $res = $this->db->Select_query("SELECT verification_status FROM freelancer_profiles WHERE user_id = '$freelancer_id'");
        $status = !empty($res) ? $res[0]['verification_status'] : 'pending';
        
        $docs = $this->db->Select_query("SELECT * FROM freelancer_documents WHERE freelancer_id = '$freelancer_id'");
        
        return ['status' => $status, 'documents' => $docs];
    }

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

    public function getPublicProfile($freelancer_id, $viewer_id = null) {
        $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);

        $data = $this->db->Select_query("
            SELECT u.name, u.email, p.* FROM user u 
            JOIN freelancer_profiles p ON u.user_id = p.user_id 
            WHERE u.user_id = '$freelancer_id'
        ");

        if (empty($data)) return null;
        $profile = $data[0];

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
public function hasCompletedProfile($user_id){

    $db = DBcontrollers::getInstance();

    $result = $db->Select_query("
        SELECT *
        FROM freelancer_profiles
        WHERE user_id = '$user_id'
        AND bio IS NOT NULL
        AND bio != ''
    ");

    return !empty($result);
}

public function adminCompleteProject($project_id, $freelancer_id, $rating) {
    $project_id = $this->db->connection->real_escape_string($project_id);
    $freelancer_id = $this->db->connection->real_escape_string($freelancer_id);
    $rating = intval($rating);

    $updateProject = $this->db->insertquery("
        UPDATE projects 
        SET status = 'Completed', 
            admin_rating = '$rating' 
        WHERE project_id = '$project_id'
    ");

    if ($updateProject) {
    
        $stats = $this->db->Select_query("
            SELECT AVG(p.admin_rating) as avg_rating, COUNT(p.project_id) as total 
            FROM projects p
            JOIN proposal pr ON p.project_id = pr.project_id
            WHERE pr.freelancer_id = '$freelancer_id' 
            AND p.status = 'Completed' 
            AND p.admin_rating IS NOT NULL
        ");

        if (!empty($stats)) {
            $new_avg = $stats[0]['avg_rating'];
            $new_total = $stats[0]['total'];

            $this->db->insertquery("
                UPDATE freelancer_profiles 
                SET rating_avg = '$new_avg', 
                    total_reviews = '$new_total' 
                WHERE user_id = '$freelancer_id'
            ");
        }
    }
    return $updateProject;
}

}