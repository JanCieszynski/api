<?php

class User {
    //właściowości klasy user czyli "co użytkownik ma"

    private $id;
    private $email;
    private $password;

    //metody klasy user czyli "co uzytkownik robi"


    //konstruktor
    public function __construct(int $id, string $email)
    {
        //this oznacza tworzony właśnie obiekt lub instancje
        $this->id =$id;
        $this->email = $email;
    }

     //getery:
     public function getEmail() : string {
        return $this->email;
    }

    public static function Register(string $email,string $password) : bool {
        // funkcja rejstruje nowego uzytkownika do bazy
        //funkcja zwraca true jesli sie udalo lub false jesli nie udalo sie
        $db = new mysqli('localhost' , 'root' , '' , 'bazazcms');
        $sql = "INSERT INTO user (email, password) VALUES (?, ?)";
        $q = $db->prepare($sql);
        $passwordHash = password_hash($password, PASSWORD_ARGON2I);
        $q->bind_param("s", $email , $passwordHash);
        $result = $q->execute();
        return $result;
    }

    public static function Login(string $email, string $password) : bool {
        //funkcja loguje istniejacego uzytkownika do bazy
        //funkcja zwraca false jesli uzytkownika o takim hasle nie istnieje
        $db = new mysqli('localhost', 'root', '', 'forum-wedkarskie');
        $sql = "SELECT * FROM user WHERE e-mail = ? LIMIT 1";
        $q = $db->prepare($sql);
        $q->bind_param("s", $email);
        $q->execute();
        $result = $q->get_result();
        $row = $result->fetch_assoc();
        //tu muszą się nazwy w nawiasach [] zgadzać z nazwą kolumny w bazie danych
        $id = $row['ID'];
        $passwordHash = $row['password'];
        if(password_verify($password, $passwordHash)) {
            //hasło się zgadza
            //zapisz dane użytkownika do sesji
            $user = new User($id, $email);
            $_SESSION['user'] = $user;
            return true;
        } else {
            //hasło się nie zgadza
            return false;
        }
    }

    public static function isLogged() {
        if(isset($_SESSION['user']))
            return true;
        else 
            return false;
    }

    public function Logout() {
        //funkcja wylogowuje uzytkownika
        session_destroy();

    }

    public function ChangePassword(string $oldPassword, string $newPassword) : bool  {
        //ta funkcja ma zaktualizować hasło użytkownika w bazie danych
        //wyciągnij hash hasła z bazy danych
        $db = new mysqli("localhost", "root", "", "forum-wedkarskie");
        $sql = "SELECT password FROM USER WHERE user.ID = ?";
        $q = $db->prepare($sql);
        $q->bind_param("i", $this->id);
        $q->execute();
        //$result to jest mysqli_result
        $result = $q->get_result();
        $row = $result->fetch_assoc();
        $oldPasswordHash = $row['password'];

        if(password_verify($oldPassword, $oldPasswordHash)){
            //użytkownik wprowadził poprawne _stare_ hasło
            $newPasswordHash = password_hash($newPassword, PASSWORD_ARGON2I);
            $sql = "UPDATE user SET password = ? WHERE user.ID = ?";
            $q = $db->prepare($sql);
            $q->bind_param("si", $newPasswordHash, $this->id);
            $result = $q->execute();
            return $result;
        } else {
            return false;
        }
    }
}

?>