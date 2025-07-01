<?php
// // config/Database.php
// class Database {
//     private $host = "localhost";
//     private $db_name = "quai_antique";
//     private $username = "root";
//     private $password = "";
//     public $conn;

//     public function getConnection() {
//         $this->conn = null;
//         try {
//             $this->conn = new PDO(
//                 "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
//                 $this->username,
//                 $this->password
//             );
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         } catch(PDOException $exception) {
//             echo "Erreur de connexion à la base de données : " . $exception->getMessage();
//         }
//         return $this->conn;
//     }
// }


class Database {
    private $host = "mysql-jobin.alwaysdata.net";
    private $db_name = "jobin_quai_antique";
    private $username = "jobin";
    private $password = "Solene4ever25<3?";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion à la base de données : " . $exception->getMessage();
        }
        return $this->conn;
    }
}

