<?php

date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

class DatabaseManager {

    private $dbhost;
    private $dbuser;
    private $dbpass;
    private $dbname;
    private $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    public function connect() {
		$this->dbhost = "sql309.infinityfree.com";
		$this->dbuser = "if0_35208251";
		$this->dbpass = "QvyB78lPape";
		$this->dbname = 'if0_35208251_database';
		$this->conn = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
		return $this->conn;
	}
	
	public function conn() {
	    return $this->conn;
	}
	
	public function close() {
	    mysqli_close($this->conn);
	}
	
	public function getHomeUrl() {
        return "https://" . $_SERVER['SERVER_NAME'] . "/";
    }
    
    public function getHomeAssets() {
        return $_SERVER['DOCUMENT_ROOT'] . "/back-api/assets/";
    }
	
	public function clearAllTable() {
        $tables = ['Users', 'UserDetails'];
        $successCount = 0;

        foreach ($tables as $table) {
            $sql = "DELETE FROM $table;";
            if ($this->conn->query($sql)) {
                echo "Successfully deleted all records from $table table<br>";
                $successCount++;
            } else {
                echo "Failed to delete records from $table table: " . $this->conn->error . "<br>";
            }
        }

        if ($successCount === count($tables)) {
            echo "All tables cleared successfully<br>";
        } else {
            echo "Some tables failed to clear<br>";
        }
    }
	
	public function createAllTable() {
        $queries = [
            [
                "table" => "Users",
                "query" => "CREATE TABLE IF NOT EXISTS Users (
                    id INT NOT NULL AUTO_INCREMENT,
                    email VARCHAR(100) NOT NULL,
                    password VARCHAR(100) NOT NULL,
                    username VARCHAR(100),
                    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    INDEX idx_email (email),
                    INDEX idx_username (username),
                    INDEX idx_updated_date (updated_date)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            ],
            [
                "table" => "UserDetails",
                "query" => "CREATE TABLE IF NOT EXISTS UserDetails (
                    id INT NOT NULL AUTO_INCREMENT,
                    type INT NOT NULL DEFAULT 0,
                    name VARCHAR(25),
                    gender ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Other',
                    lang_code VARCHAR(100),
                    mention_id VARCHAR(100),
                    last_accessed DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    about VARCHAR(225),
                    photo VARCHAR(100),
                    PRIMARY KEY (id),
                    INDEX idx_mention_id (mention_id),
                    INDEX idx_lang_code (lang_code),
                    INDEX idx_name (name),
                    FOREIGN KEY (id) REFERENCES Users (id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            ]
        ];
        
        foreach ($queries as $queryData) {
            $table = $queryData["table"];
            $query = $queryData["query"];
    
            if ($this->conn->query($query) === TRUE) {
                echo "Tabel $table berhasil dibuat<br>";
            } else {
                echo "Error saat membuat tabel $table: " . $conn->error . "<br>";
            }
        }
    }
    
    public function deleteAccount($userId) {
        $deleteUserQuery = "DELETE FROM `Users` WHERE `id` = $userId";
        if (mysqli_query($this->conn, $deleteUserQuery)) {
            $this->deleteUserData($userId);
            return array(
                "ok" => true,
                "text" => "User deleted successfully"
            );
        } else {
            return array(
                "ok" => false,
                "text" => "Error deleting user: " . mysqli_error($conn)
            );
        }
    }
    
    public function deleteUserData($userId) {
        $file_path = $this->getHomeAssets() . $userId;
        if (file_exists($file_path)) {
			if (unlink($file_path)) {
				//berhasil di hapus
			} else {
				//gagal menghapus file
			}
		} else {
			//File tidak ditemukan
		}
    }
    
    public function changeName($id, $name) {
		$name = mysqli_real_escape_string($this->conn, $name);
		
		$data = "UPDATE UserDetails SET name = '$name' WHERE id = '$id';";
		if (mysqli_query($this->conn, $data)) {
			$response = array(
			"ok" => true,
			"code" => 0,
			"name" => $name,
			"text" => "Success"
			);
		} else {
			$response = array(
			"ok" => false,
			"code" => 1,
			"text" => "Something went wrong"
			);
		}
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
    
	public function changeUserGender($id, $gender) {
		$gender = mysqli_real_escape_string($this->conn, $gender);
		
		$data = "UPDATE UserDetails SET gender = '$gender' WHERE id = '$id';";
		if (mysqli_query($this->conn, $data)) {
			$response = array(
			"ok" => true,
			"code" => 0,
			"gender" => $gender,
			"text" => "Success"
			);
		} else {
			$response = array(
			"ok" => false,
			"code" => 1,
			"text" => "Something went wrong"
			);
		}
		echo json_encode($response, JSON_PRETTY_PRINT);
	}
	
	public function changeUserBio($id, $about) {
		$about = mysqli_real_escape_string($this->conn, $about);
		
		$data = "UPDATE UserDetails SET about = '$about' WHERE id = '$id';";
		if (mysqli_query($this->conn, $data)) {
			$arr = array (
			"ok" => true,
			"code" => 0,
			"about" => $about,
			"text" => "Succes"
			);
			echo json_encode($arr, JSON_PRETTY_PRINT);
		} else {
			$arr = array (
			"ok" => false,
			"code" => 1,
			"text" => "What's wrong"
			);
			echo json_encode($arr, JSON_PRETTY_PRINT);
		}
	}
	
	public function updateUserName($id, $username) {
        $username = mysqli_real_escape_string($this->conn, $username);
        
        if (!$this->checkUserNameIsAvailableBool($username)) {
            $arr = array (
                "ok" => false,
                "code" => 1,
                "text" => "USERNAME_INVALID"
            );
            echo json_encode($arr, JSON_PRETTY_PRINT);
            exit;
        }
        
        $data = "UPDATE Users SET username = '$username' WHERE id = '$id';";
        if (mysqli_query($this->conn, $data)) {
            $arr = array (
                "ok" => true,
                "code" => 0,
                "text" => $username
            );
            echo json_encode($arr, JSON_PRETTY_PRINT);
        } else {
            $arr = array (
                "ok" => false,
                "code" => 1,
                "text" => "What's wrong"
            );
            echo json_encode($arr, JSON_PRETTY_PRINT);
        }
    }
    
    private function checkUserNameIsAvailableBool($username) {
        $req = mysqli_query($this->conn, "SELECT 1 FROM Users WHERE BINARY username='$username' LIMIT 1;");
        if (mysqli_num_rows($req) > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    public function checkUserNameIsAvaible($username) {
        if ($this->checkUserNameIsAvailableBool($username)) {
            $arr = array (
                "ok" => true,
                "code" => 0,
                "text" => "Success"
            );
            echo json_encode($arr, JSON_PRETTY_PRINT);
        } else {
            $arr = array (
                "ok" => false,
                "code" => 1,
                "text" => "Username is not available"
            );
            echo json_encode($arr, JSON_PRETTY_PRINT);
        }
    }
}
