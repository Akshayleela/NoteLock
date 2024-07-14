<?php

class Database {
    public $conn; // it should be public so that it canbe accessed by other files also
    private $dbname = 'NLP';
    private $usersTable = 'users';
    private $notesTable = 'notes';
    private $passwordsTable = 'passwords';
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';

    public function __construct() {
        // Establish database connection
        $this->conn = new mysqli($this->host, $this->username, $this->password);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Create database if it doesn't exist
        $this->createDatabase();

        // Select the database
        $this->conn->select_db($this->dbname);

        // Create tables if they don't exist
        $this->createUsersTable();
        $this->createNotesTable();
        $this->createPasswordsTable();
    }

    private function createDatabase() {
        $sql = "CREATE DATABASE IF NOT EXISTS {$this->dbname}";
        $this->conn->query($sql);
        if ($this->conn->error) {
            die("Error creating Database : " . $this->conn->error);
        }
    }

    private function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->usersTable} (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(250) NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->query($sql);
        if ($this->conn->error) {
            die("Error creating table 'Users': " . $this->conn->error);
        }
    }

    private function createNotesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->notesTable} (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            title VARCHAR(250) NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES {$this->usersTable}(id) ON DELETE CASCADE
        )";
        $this->conn->query($sql);
        if ($this->conn->error) {
            die("Error creating table 'notes': " . $this->conn->error);
        }
    }

    private function createPasswordsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->passwordsTable} (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            sitename VARCHAR(250) NOT NULL,
            password TEXT NOT NULL,
            description TEXT,
            siteURL VARCHAR(250),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES {$this->usersTable}(id) ON DELETE CASCADE
        )";
       $this->conn->query($sql);
       if ($this->conn->error) {
           die("Error creating table 'passwords': " . $this->conn->error);
       }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

?>
