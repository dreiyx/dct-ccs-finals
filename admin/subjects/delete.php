<?php
// Include necessary files
include '../partials/header.php';
include '../partials/side-bar.php';
include '../../functions.php';

// Initialize variables
$subjectToDelete = null;

// Check if subject_code is set in the URL
if (isset($_GET['subject_code'])) {
    $subjectCode = $_GET['subject_code'];

    // Fetch subject details to display for confirmation
    $subjects = getSubjects();

    foreach ($subjects as $subject) {
        if ($subject['subject_code'] === $subjectCode) {
            $subjectToDelete = $subject;
            break;
        }
    }

    // Handle deletion if confirmed
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = deleteSubject($subjectCode);
            if ($result) {
                // Redirect to add.php after successful deletion
                header('Location: index.php');
                exit();
            } else {
                echo "<p style='color: red;'>Error: Could not delete the subject record. Please try again.</p>";
            }
        } catch (Exception $e) {
            error_log("Error deleting subject: " . $e->getMessage());
            echo "<p style='color: red;'>An unexpected error occurred. Please check the logs.</p>";
        }
    }
} else {
    echo "<p style='color: red;'>No subject code provided in the URL.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Subject</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .breadcrumb {
            margin-bottom: 20px;
        }
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
            margin-right: 5px;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .breadcrumb span {
            margin-right: 5px;
        }
        .confirmation-box {
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 5px;
        }
        .confirmation-box p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .confirmation-box ul {
            list-style-type: none;
            padding: 0;
        }
        .confirmation-box ul li {
            margin-bottom: 10px;
        }
        .confirmation-box ul li strong {
            font-weight: bold;
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .buttons a, .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
        }
        .buttons .cancel-btn {
            background-color: #6c757d;
        }
        .buttons .delete-btn {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete Subject</h1>
        <div class="breadcrumb">
            <a href="#">Dashboard</a> / 
            <a href="add.php">Add Subject</a> / 
            <span>Delete Subject</span>
        </div>
        <div class="confirmation-box">
            <?php if ($subjectToDelete): ?>
                <p>Are you sure you want to delete the following subject record?</p>
                <ul>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subjectToDelete['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subjectToDelete['subject_name']); ?></li>
                </ul>
                <form method="POST">
                    <div class="buttons">
                        <a href="add.php" class="cancel-btn">Cancel</a>
                        <button type="submit" class="delete-btn">Delete Subject Record</button>
                    </div>
                </form>
            <?php else: ?>
                <p style="color: red;">Subject not found. Please check the URL and try again.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
