<?php

namespace Data\Datasource;

require_once __DIR__ . '/../model/AppResponse.php';

// Imports
use Data\Model\AppResponse;
use Data\Model\InsertParams;
use Data\Model\LoginParams;
use Data\Model\RegisterParams;
use Data\Model\ResponseError;
use Data\Model\ResponseSuccess;
use PDO;
use PDOException;

class LocalDataSource {
    private $pdo;

    public function __construct() {
        
        // ███╗░░░███╗██╗░░░██╗░██████╗░██████╗░██╗░░░░░
        // ████╗░████║╚██╗░██╔╝██╔════╝██╔═══██╗██║░░░░░
        // ██╔████╔██║░╚████╔╝░╚█████╗░██║██╗██║██║░░░░░
        // ██║╚██╔╝██║░░╚██╔╝░░░╚═══██╗╚██████╔╝██║░░░░░
        // ██║░╚═╝░██║░░░██║░░░██████╔╝░╚═██╔═╝░███████╗
        // ╚═╝░░░░░╚═╝░░░╚═╝░░░╚═════╝░░░░╚═╝░░░╚══════╝

        // Before using the app, ensure MySQL is configured: 
        // start XAMPP's MySQL server and adjust host, dbname, username, 
        // and password settings to enable seamless operation of the application.

        // Host (default: localhost)
        $host = "localhost";
        // Database Name
        $dbname = "meal_plan";
        // Username
        $username = "root";
        // Password
        $password = "";

        $dsn = "mysql:host=$host;dbname=$dbname";
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        try {
             // Creates Table if it Doesnt exist in Database
            $stmt = $this->pdo->prepare("
            CREATE TABLE IF NOT EXISTS history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(50) NOT NULL,
            recipeId INT NOT NULL,    
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Execute Create table Query
            $stmt->execute();
            error_log("Table 'history' Successfully Created");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        try{
            // Creates Table if it Doesnt exist in Database
             $stmt = $this->pdo->prepare("
            CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,    
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Execute Create table Query
            $stmt->execute();
            error_log("Table 'user' Successfully Created");
        }catch (PDOException $e){
            die("Database connection failed: " . $e->getMessage());
        }
    }

    //Register
    public function registerUser(RegisterParams $params): AppResponse {
        $username = $params->getUsername();
        $password = $params->getPassword();

        try{
            $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (? , ?)");
            $stmt->execute([$username, $password]);
            return new ResponseSuccess("User Successfully Inserted");
        } catch (PDOException $e) {
            return new ResponseError(500, $e->getMessage());
        }
    }

    //Login
    public function Loginuser(LoginParams $params): AppResponse {
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM users");
            $stmt->execute();

            $login = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return new ResponseSuccess(json_encode($login));
        }catch(PDOException $e){
            return new ResponseError(500, $e->getMessage());
        }
    }

   // Get Recent Meals History
   public function getMealsHistory(): AppResponse  {
    
    try {
        $stmt = $this->pdo->prepare("SELECT * FROM history");
        $stmt->execute();
        
        // Fetch all rows as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return a ResponseSuccess with the fetched data as JSON:3
        return new ResponseSuccess(json_encode($data));
    } catch (PDOException $e) {            
        return new ResponseError(500, $e->getMessage());
    }
   }

    // Saves Recent Opened Meal
    public function insertMealHistory(InsertParams $params): AppResponse {
        $recipeId = $params->getRecipeId();
        $title = $params->getTitle();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO history ( recipeId, title) VALUES (?, ?)");
            $stmt->execute([$recipeId, $title ]);
            return new ResponseSuccess("Recipe Successfully Inserted");
        } catch (PDOException $e) {            
            return new ResponseError(500, $e->getMessage());
        }
    }

    // Clear Meal History
    public function clearMealHistory(): AppResponse {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM history");
            $stmt->execute();
            return new ResponseSuccess("History Successfuly Cleared");
        } catch (PDOException $e) {
            return new ResponseError(500, $e->getMessage());
        }
    }

    
   
}
?>
