<?php

require_once("loginView.php");
require_once("loginModel.php");
require_once("AddUserView.php");



class login{

    private $m_loginView;
    private $m_loginModel;
    private $m_AddUserView;

    public function __construct(){
        $this->m_AddUserView = new AddUserView();
        $this->m_loginView = new loginView();
        $this->m_loginModel = new loginModel();

    }


    public function doControll(){

        //My code for lab4 start here and goes down to row 50.

        if($this->m_loginView->didUserPressAddUser()){
            $this->m_AddUserView->valueHandler($this->m_loginModel->getUsername());
            $this->m_loginModel->addFormSession();
            return  $this->m_AddUserView->AddUserForm();

        }
        //if user pressed login, a session i started to keep track where he is navigating
        // since the code i got didnt use any form actions or gets i kept it that way
        if(isset($_SESSION["addform"])){
            //check if the registerbutton was clicked
            if($this->m_AddUserView->getNewUserInfo()){
                //compares if the input was valid
                if($this->m_loginModel->compareAddUserInfo($this->m_AddUserView->getUsername(), $this->m_AddUserView->getPassword(), $this->m_AddUserView->getPasswordVerification())){
                    //inserts the information to db
                    $this->m_loginModel->insertUserToDB($this->m_AddUserView->getUsername(), $this->m_AddUserView->getPassword());

                    $this->m_loginModel->Login();
                    $this->m_loginView->setAgent();
                    $this->m_loginModel->setAgent($this->m_loginView->getAgent());
                    return $this->m_loginView->DisplaySuccessfulLogin();

                }elseif($_SESSION["addform"]) {
                    //presenting error msgs
                    $this->m_AddUserView->errorHandler($this->m_loginModel->getErrorMessage());
                    $this->m_AddUserView->valueHandler($this->m_loginModel->getUsername());
                    return $this->m_AddUserView->AddUserForm();
                }else {
                    // incase any of the earlier didnt come through the user wants to get back to login page
                    unset($_SESSION["addform"]);
                    $this->m_loginView->showLoginLogout();
                }
            }
        }

        $this->m_loginView->setAgent2();
        if($this->m_loginModel->isLoggedIn() && $this->m_loginModel->compareAgent($this->m_loginView->getAgent2())){
            if($this->m_loginView->didUserLogout()){
                $this->m_loginModel->Logout();
                $this->m_loginView->DisplayUserPressedLogout();

            }else
            {
                $this->m_loginModel->loggedInUser($this->m_AddUserView->getUsername());
                $this->m_loginView->loggedInUserHandler($this->m_loginModel->getUsername());
                $this->m_loginView->DisplayAlreadyLoggedin();

            }
        }
        elseif(!$this->m_loginModel->isLoggedIn() && $this->m_loginView->loadUserCookies() != NULL && $this->m_loginView->loadPassCookies() != NULL){

            if($this->m_loginView->checkCookieTime())
            {
                if($this->m_loginModel->comparePasswordSucced($this->m_loginView->loadUserCookies(), $this->m_loginModel->decodePassword($this->m_loginView->loadPassCookies())))
                {
                    $this->m_loginView->DisplaySuccessLoginCookieNoSess();
                    $this->m_loginModel->Login();
                    $this->m_loginView->setAgent();
                    $this->m_loginModel->setAgent($this->m_loginView->getAgent());
                }
                else{
                    $this->m_loginView->DisplayWrongCookieDetNoSess();
                }
            }else{

                $this->m_loginView->DisplayTryManipulateCookieNoSess();
            }

        }




        if($this->m_loginView->didUserLogin())
        {
            //kanske måste vara såhär på webbhotelet;
            //$username = $this->m_loginView->getUsername();
            $inputUsername = $this->m_loginView->getUsername();
            $inputPassword = $this->m_loginView->getPassword();

            if(empty($inputUsername) && empty($inputPassword))
            {
                $this->m_loginView->DisplayEmpty();

            }
            elseif(empty($inputUsername) && $this->m_loginView->getPassword())
            {
                $this->m_loginView->DisplayEmptyUsername();
            }
            elseif(empty($inputPassword) && $this->m_loginView->getUsername())
            {
                $this->m_loginView->DisplayEmptyPassword();
            }

            elseif($this->m_loginModel->comparePasswordSucced($this->m_loginView->getUsername(), $this->m_loginView->getPassword()))
            {

                $this->m_loginModel->Login();
                $this->m_loginView->setAgent();
                $this->m_loginModel->setAgent($this->m_loginView->getAgent());


                if($this->m_loginView->getCheckboxStatus())
                {
                    $this->m_loginView->makeUserCookies($this->m_loginView->getUsername());
                    $this->m_loginView->makePasswordCookies($this->m_loginModel->encryptPassword($this->m_loginView->getPassword()));
                    $this->m_loginView->DisplaySuccessLoginCookie();



                }
                else{
                    $this->m_loginModel->loggedInUser($inputUsername);
                    $this->m_loginView->loggedInUserHandler($this->m_loginModel->getUsername());

                    $this->m_loginView->DisplaySuccessfulLogin();
                }

            }
            elseif($this->m_loginModel->comparePasswordWrongPass(
                $this->m_loginView->getUsername(), $this->m_loginView->getPassword()
            )
            )
            {
                $this->m_loginView->DisplayCorrUserWrongPass();
            }
            elseif(
            $this->m_loginModel->comparePasswordWrongUsername(
                $this->m_loginView->getUsername(), $this->m_loginView->getPassword()
            )
            )
            {
                $this->m_loginView->DisplayWrongUserCorrPass();
            }
            elseif(
            $this->m_loginModel->comparePasswordAllWrong(
                $this->m_loginView->getUsername(), $this->m_loginView->getPassword()
            )
            )
            {
                $this->m_loginView->DisplayAllWrong();
            }

        }



        if(!$this->m_loginModel->isLoggedIn() && !$this->m_loginView->didUserLogout() && !$this->m_loginView->didUserLogin()
            && $this->m_loginView->loadUserCookies() == NULL && $this->m_loginView->loadPassCookies() == NULL
        )
        {
            $this->m_loginView->showLoginLogout();

        }


    }
}
?>
