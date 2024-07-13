<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("bdd.php");
class users {
    private $pdo;
    private $auth;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getlist() {
      
        $sql = 'SELECT id, name, email FROM users';
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }

    public function getinfo($id) {
        $sql = 'SELECT name, email, id FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $sql = 'UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);
        $stmt->execute();
    }

    public function create($name, $email, $password) {
        $sql = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":name" => $name, ":email" => $email, ":password" => $password]);
        if ($stmt->rowCount()) {
            header('Location: index.php?message=inserted');
            exit;
        } else {
            header('Location: index.php?message=something went wrong!');
            exit;
        }
    }
    public function verif_u($email, $password) {
        $sql = 'SELECT id FROM users WHERE email = :email AND password = :password';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email, 'password' => $password]);
        $result = $stmt->fetch();
        if ($result) {
            $_SESSION['role'] = 'admin'; // Assuming 'admin' role upon successful login
        } else {
            $_SESSION['role'] = 'guest'; // Set a default role or handle invalid login attempts
        }
    }
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id" => $id]);
    }
}
?>
