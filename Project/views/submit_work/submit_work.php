<?php
session_start();
require "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/deliveriescontrollers.php";
require_once "../../Models/deliveries.php";


$project_id = $_GET['project_id'] ?? null;

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
            header("Location: ../../views/Freelancer/freelancer-dashboard.php");
            exit;
        } else {
            echo "Database Error: Could not insert delivery.";
        }
    } else {
        echo "Upload Failed. Check folder permissions.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Work</title>
    </head>
<body>
    <h2>Submit Your Work</h2>
    
    <form action="submit_work.php?project_id=<?php echo htmlspecialchars($project_id); ?>" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

        <div style="margin-bottom: 15px;">
            <label>Message to Client:</label><br>
            <textarea name="message" placeholder="Describe your work..." required rows="5" style="width: 100%;"></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label>Work File (ZIP, PDF, DOCX):</label><br>
            <input type="file" name="work_file" required>
        </div>

        <button type="submit" class="btn-primary">Upload and Submit</button>
    </form>
</body>
</html>