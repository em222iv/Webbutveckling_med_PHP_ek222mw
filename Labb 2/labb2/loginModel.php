<?php

session_start();

class loginModel{

    protected $dbUsername = 'root';
    protected $dbPassword = 'root';
    protected $dbConnstring = 'mysql:host=localhost;dbname=users';
    protected $dbConnection;
    protected $dbTable;

	private function connectdb(){

        /*$mysqli = new mysqli("localhost", "root", "root", "users");
        return $mysqli;*/

        if ($this->dbConnection == NULL)
            $this->dbConnection = new \PDO($this->dbConnstring, $this->dbUsername, $this->dbPassword);

        $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbConnection;
    }


    //fenfewnfwfwlfmlwäe
    public function insertUserToDB($name,$pass){

        $db = $this->connectdb();

        $sql = "INSERT INTO users (username,password) VALUES (:username,:password)";

            $q = $db->prepare($sql);
            $q->execute(array(':username'=>$name,
                              ':password'=>$pass));

    }

    public function compareAddUserInfo($username,$password) {



        if(strlen($password) < 6){
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

        if($username == $db_username)
        {
            return false;
        }


        return true;

    }


	public function comparePasswordSucced($username, $password){

      /*  $mysqli = $this->connectdb();
		$sql = sprintf("SELECT *
                        FROM users
                        WHERE username = %u", $username);
        $result = $mysqli->query($sql);

        while($db_field = mysqli_fetch_assoc($result)) {
        var_dump($db_field['username']);
		$db_username = $db_field['username'];
		$db_password = $db_field['password'];
        }*/

        $db = $this->connectdb();

        $sql = "SELECT * FROM users WHERE username  = ?";
        $params = array($username);

        $query = $db -> prepare($sql);
        $query -> execute($params);

        $result = $query -> fetch();
        $db_username = $result[1];
        $db_password = $result[2];

		 if($username == $db_username && $password == $db_password)
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

		 if($username == $db_username && $password !== $db_password)
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

		 if($username !== $db_username && $password == $db_password)
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

		 if($username !== $db_username && $password !== $db_password)
		 {
		 	return true;
		 }

		 return false;


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


	

}