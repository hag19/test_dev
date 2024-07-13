<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('bdd.php');
include('class.php');
session_start();

$user = new users($pdo);

$requestMethod = $_SERVER['REQUEST_METHOD'];
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    if($auth->verif_u(htmlspecialchars($_POST['email']),hash('sha256', htmlspecialchars($_POST['password'])))){
    echo json_encode(["message" => "now you can!"]);
    exit;
    }else{
    http_response_code(403);
    echo json_encode(["message" => "Unauthorized access"]);
    exit;
    } 
}else{
switch ($requestMethod) {
    case 'DELETE':
        // Check if user is authenticated
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403); // Forbidden
            echo json_encode(["message" => "Unauthorized access"]);
            exit;
        }

        // Read the input stream for JSON data
        $input = file_get_contents('php://input');
        // Decode JSON data
        $data = json_decode($input, true);

        if (isset($data['id'])) {
            try {
                $user->delete($data['id']);
                http_response_code(200);
                echo json_encode(["message" => "User deleted successfully"]);
            } catch (PDOException $e) {
                // Handle errors if the delete operation fails
                http_response_code(500);
                echo json_encode(["message" => "Failed to delete user: " . $e->getMessage()]);
            }
        } else {
            // Handle invalid or missing data
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON format or missing data"]);
        }
        break;

    case 'POST':
        // Check if user is authenticated
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            // Redirect or handle unauthorized access
         $auth->verif_u(htmlspecialchars($_POST['email']),hash('sha256', htmlspecialchars($_POST['password'])));
        header('location: index.php');
        exit;
        }

        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $password = hash('sha256', htmlspecialchars($_POST['password']));

        try {
            $user->create($name, $email, $password);
            http_response_code(200);
            echo json_encode(["message" => "User created successfully"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create user: " . $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Check if user is authenticated
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403); // Forbidden
            echo json_encode(["message" => "Unauthorized access"]);
            exit;
        }

        // Read the input stream for JSON data
        $input = file_get_contents('php://input');
        // Decode JSON data
        $data = json_decode($input, true);

        if (isset($data['id'])) {
            try {
                $user->update($data);
                http_response_code(200);
                echo json_encode(["message" => "User updated successfully"]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update user: " . $e->getMessage()]);
            }
        } else {
            // Handle invalid or missing data
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON format or missing data"]);
        }
        break;

    case 'GET':
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'getlist') {
                // Check if user is authenticated
                if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                    http_response_code(403); // Forbidden
                    echo json_encode(["message" => "Unauthorized access"]);
                    exit;
                }

                $user->getlist();
            } elseif ($_GET['action'] == 'getuser') {
                // Check if user is authenticated
                if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                    http_response_code(403); // Forbidden
                    echo json_encode(["message" => "Unauthorized access"]);
                    exit;
                }

                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $data = $user->getinfo($id);
                    echo json_encode($data);
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid action or missing ID"]);
                }
            } else if ($_GET['action'] == 'getsession') {
                if (isset($_SESSION['role'])) {
                    $response = [
                        "role" => $_SESSION['role']
                    ];
                } else {
                    $response = ["error" => "No session data found."];
                }
                echo json_encode($response);
            } 
        } else {
            http_response_code(400);
            echo json_encode(["message" => "No action specified"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
}
?>
