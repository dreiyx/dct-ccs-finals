<?php    
    session_start();
    
    function openCon() {
        $conn = new mysqli("localhost", "root", "", "dct-ccs-finals");
    
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
    
    function closeCon($conn) {
        $conn->close();
    }
    
    function debugLog($message) {
        error_log("[DEBUG] " . $message);
    }
    
    function loginUser($username, $password) {
        $conn = openCon();
    
        // Query to fetch user data by email
        $sql = "SELECT * FROM users WHERE email = ?"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
    
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
    
            // Verify password using password_verify() with the hash stored in the database
            if (password_verify($password, $user['password'])) {
                // Set session variables for logged-in user
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_id'] = $user['id'];
    
                $stmt->close();
                closeCon($conn);
                return true;
            }
        }
    
        $stmt->close();
        closeCon($conn);
        return false;
    }
    
    function isLoggedIn() {
        return isset($_SESSION['email']);
    }
    
    function addUser() {
        $conn = openCon();
    
        if ($conn) {
            // User input (usually from a form)
            $email = 'user2@gmail.com'; // Example email
            $password = 'password'; // Example password
            $name = 'user2'; // Example name
            
            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Use password_hash for encryption
    
            // Use prepared statements to prevent SQL injection
            $sql = "INSERT INTO users (email, password, name) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $hashedPassword, $name); // Bind parameters
    
            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
    
            $stmt->close();
            closeCon($conn);
        } else {
            echo "Failed to connect to the database.";
        }
    }

    function addSubject($subjectCode, $subjectName) {
        $conn = openCon();
        
        // Prepare the SQL query to insert a new subject
        $sql = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $subjectCode, $subjectName);
        
        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            debugLog("Subject added: $subjectCode - $subjectName");
        } else {
            debugLog("Error adding subject: " . $stmt->error);
        }
        
        // Close the statement and connection
        $stmt->close();
        closeCon($conn);
    }
    
    function getSubjects() {
        $conn = openCon();
        
        // Fetch all subjects from the database
        $sql = "SELECT * FROM subjects";
        $result = $conn->query($sql);
    
        // Initialize an array to store the subjects
        $subjects = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $subjects[] = $row;
            }
        }
        
        // Close the connection
        closeCon($conn);
        
        return $subjects;
    }

    function deleteSubject($subject_code) {
        $conn = openCon(); // Use the existing function for connection
    
        // Prepare and execute delete query
        $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_code = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
    
        $stmt->bind_param("s", $subject_code);
        $result = $stmt->execute();
    
        if (!$result) {
            error_log("Error deleting subject: " . $stmt->error); // Log error for debugging
            $stmt->close();
            closeCon($conn);
            return false; // Return false if deletion fails
        }
    
        // Close the statement and connection
        $stmt->close();
        closeCon($conn);
        return true; // Return true if deletion was successful
    }
?>
