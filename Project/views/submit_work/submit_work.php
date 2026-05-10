<?php
session_start();
require "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/deliveriescontrollers.php";
require_once "../../Models/deliveries.php";



$db = DBcontrollers::getInstance();


$project_id = $_GET['project_id'] ?? null;

$project_status_res = $db->Select_query("SELECT status FROM projects WHERE project_id = '$project_id'");
$project_status = $project_status_res[0]['status'] ?? '';

if ($project_status == 'Completed') {
    die("This project is already completed. You cannot submit more work.");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $project_id = $_POST['project_id'] ?? null;
    
    if (!$project_id || empty($project_id)) {
        die("Error: Project ID is missing. Cannot submit work.");
    }

    $message = $_POST['message'];
    $freelancer_id = $_SESSION['userid'];
    $file = $_FILES['work_file'];

    $filename = time() . "_" . basename($file['name']);
    $allowed = ['zip', 'pdf', 'docx', 'jpg', 'png']; 
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type. Allowed types: zip, pdf, docx, jpg, png");
    }

    $target = "../../upload/deliveries/" . $filename;

    if (!is_dir("../../upload/deliveries/")) {
        mkdir("../../upload/deliveries/", 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $delivery = new deliveries();
        $delivery->project_id = $project_id; 
        $delivery->freelancer_id = $freelancer_id;
        $delivery->message = $message;
        $delivery->file_path = $filename;

        $controller = new deliveriescontrollers();
        $result = $controller->submit_work($delivery);
        if ($result) {

            $project_data = $db->Select_query(" SELECT client_id, title FROM projects WHERE project_id = '$project_id'");

    if (!empty($project_data)) {

        $client_id = $project_data[0]['client_id'];
        $project_title = $project_data[0]['title'];

        $title = "New Work Submission";
        $msg = "Freelancer submitted work for project: " . $project_title;

        $insert_notification = "
            INSERT INTO notification(user_id, title, msg)
            VALUES('$client_id', '$title', '$msg')
        ";

        $db->insertquery($insert_notification);
    }

    header("Location: ../../views/Freelancer/freelancer-dashboard.php");
    exit;

}
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Work</title>
    <link rel="stylesheet" href="../../public/assets/css/submit-work.css">
    </head>
<body>

<div class="submit-container">

    <h2 class="submit-title">Submit Your Work</h2>

    <form action="submit_work.php?project_id=<?php echo htmlspecialchars($project_id); ?>" method="POST"
        enctype="multipart/form-data">

        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

        <div class="form-group">

            <label>Message to Client:</label>

            <textarea name="message" placeholder="Describe your work..." required rows="5"></textarea>

        </div>

        <div class="form-group">

            <label>Work File (ZIP, PDF, DOCX):</label>

            <input type="file" name="work_file" required>

        </div>

        <button type="submit" class="submit-btn">  Upload and Submit
        </button>

    </form>

</div>

</body>
</html>