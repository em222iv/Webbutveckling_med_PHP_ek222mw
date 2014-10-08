<?php

session_start();

class loginModel{

    protected $dbUsername = 'eerie_se';
    protected $dbPassword = 'NyUYN8xk';
    protected $dbConnstring = 'mysql:host=eerie.se.mysql;dbname=eerie_se';
    protected $dbConnection;
    protected $dbTable;
    private $errorMessage;
    private $username;

    //connection was earlier in mysql. changed to PDO.
	private function connectdb(){

        if ($this->dbConnection == NULL)
            $this->dbConnection = new \PDO($this->dbConnstring, $this->dbUsername, $this->dbPassword);

        $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbConnection;
    }

    //inserts user info
    public function insertUserToDB($name,$pass){

        $db = $this->connectdb();

        $sql = "INSERT INTO users (username,password) VALUES (:username,:password)";

            $q = $db->prepare($sql);

            $hash = password_hash($pass,PASSWORD_BCRYPT);

            $q->execute(array(':username'=>$name,
                              ':password'=>$hash));

    }
    //checks if any of the input was faulty
    public function compareAddUserInfo($username,$password,$verifyPassword) {

        if(strlen($username) < 3 && strlen($password) < 6 && strlen($verifyPassword < 6)){
            $this->errorMessage = "Användarenamet är för kort. Minst 3 tecken.</br> Lösenordet är för kort. Minst 6 tecken";
            return false;
        }

        if(strcmp($password, $verifyPassword) !== 0){
            $this->errorMessage = "Lösenorden matcher inte";
            return false;
        }
        if(strlen($password) < 6){
            $this->errorMessage = "Lösenordet är för kort. Minst 6 tecken";
            return false;
        }
        if(strlen($verifyPassword) < 6){
            $this->errorMessage = "Lösenordet är för kort. Minst 6 tecken";
            return false;
        }
        if(strlen($username) < 3){
             $this->errorMessage = "Användarenamet är för kort. Minst 3 tecken";
            return false;
        }

        $db = $this->connectdb();


        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $db_username = $result[1];
        $db_password = $result[2];

        if($username == $db_username || strlen($username) < 3){
            $this->errorMessage = "Användarnamnet är redan upptaget";
            return false;
        }
        if($username != strip_tags($username)) {
            $this->username = strip_tags($username);
            $this->errorMessage = "Användarnamnet innehåller ogiltiga tecken";
            return false;
        }


        return true;

    }


    //I would have wanted to combine the compare methods from here on. But i ran out of time.
	public function comparePasswordSucced($username, $password){

        $db = $this->connectdb();
        $_SESSION["username"] = $username;

        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $db_username = $result[1];
        $db_password = $result[2];


		 if($username == $db_username && password_verify($password, $db_password))
		 {
		 	return true;
		 }

		 return false;


	}

	public function encryptPassword($pw){

		return base64_encode($pw);
	}

	public function decodePassword($pwcrypt){

		return base64_decode($pwcrypt);
	}



	public function comparePasswordWrongPass($username, $password){

        $db = $this->connectdb();

        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $db_username = $result[1];
        $db_password = $result[2];

		 if($username == $db_username && !password_verify($password, $db_password))
		 {
		 	return true;
		 }

		 return false;




	}	

	public function comparePasswordWrongUsername($username, $password){

        $db = $this->connectdb();

        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $db_username = $result[1];
        $db_password = $result[2];

		 if($username !== $db_username && password_verify($password, $db_password))
		 {
		 	return true;
		 }

		 return false;




	}	

	public function comparePasswordAllWrong($username, $password){

        $db = $this->connectdb();

        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $db_username = $result[1];
        $db_password = $result[2];

		 if($username !== $db_username && !password_verify($password, $db_password))
		 {
		 	return true;
		 }

		 return false;


	}

    public function loggedInUser($username) {
        $db = $this->connectdb();

        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $this->username = $result[1];

    }

    public function addFormSession() {
        $_SESSION["addform"] = true;
    }

	public function isLoggedIn()
	{


		if(isset($_SESSION["SessionUsername"])){

		$saveUserSession = $_SESSION["SessionUsername"];


		return true;
		}
		return false;
	}

	public function Logout(){

		
		session_unset($_SESSION["SessionUsername"]);
		setcookie("Username",NULL);
		setcookie("Password", NULL);


	}

	public function Login(){

		$_SESSION["SessionUsername"] = true;

	}

	public function setAgent($agent){

		if(isset($_SESSION["SessionAgent"]) == false)
		{
			$_SESSION["SessionAgent"] = $agent;
			return true;
		}
		return false;
	}

	public function compareAgent($agent){
			
		if($_SESSION['SessionAgent'] === $agent){
			return true;

		} 
		return false;
	}

    public function getErrorMessage() {
        return $this->errorMessage;
    }
    public function getUsername() {
        return $this->username;
    }

    public function getUsernameFromSession() {
        return $_SESSION['username'];
    }
}