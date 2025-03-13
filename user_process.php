<?php

require_once("globals.php");
require_once("db.php");
require_once("./models/User.php");
require_once("./models/Message.php");
require_once("./dao/UserDAO.php");

$message = new Message($BASE_URL);
$userDAO = new userDAO($conn, $BASE_URL);

// Verifica o tipo do formulario
$type = filter_input(INPUT_POST, "type");

// Atualizar usuario
if($type === "update"){
    
    // Resgata dados do usuario
    $userData = $userDAO->verifyToken();

    // Receber dados do POST
    $name = filter_input(INPUT_POST, "name");
    $lastname = filter_input(INPUT_POST, "lastname");
    $email = filter_input(INPUT_POST, "email");
    $bio = filter_input(INPUT_POST, "bio");

    // Criar um novo objeto de usuario
    $user = new User();

    // Preencher os dados do usuario
    $userData->name = $name;
    $userData->lastname = $lastname;
    $userData->email = $email;
    $userData->bio = $bio;

    // Upload da imagem 
    if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
        $image = $_FILES["image"];
        $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
        $jpgArray = ["image/jpeg", "image/jpg"];

        // Checagem de tipo de imagem
        if (in_array($image["type"], $imageTypes)) {

            // Processamento da imagem
            if (in_array($image["type"], $jpgArray)) {

                // A imagem é JPEG ou JPG
                $imageFile = imagecreatefromjpeg($image["tmp_name"]);
            } else {

                // A imagem é PNG
                $imageFile = imagecreatefrompng($image["tmp_name"]);
            }

            // Gerar um nome único para a imagem
            $imageName = $user->imageGenerateName();

            // Salvar a imagem como JPEG com qualidade máxima
            imagejpeg($imageFile, "./img/users/" . $imageName, 100);

            // Salvar o nome da imagem no banco de dados
            $userData->image = $imageName;

        } else {
            $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
        }
    } 

    $userDAO->update($userData);
    
// Atualizar senha do usuario
} else if($type === "changepassword"){

    // Receber dados do POST
    $password = filter_input(INPUT_POST, "password");
    $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

    // Resgata dados do usuario
    $userData = $userDAO->verifyToken();
    $id = $userData->id;

    if(!$password && !$confirmpassword){
        $message->setMessage("Por favor, preencha todos os campos.", "error", "back");
        exit;
    }
    
    if($password == $confirmpassword){

        // Criar um novo objeto de usuario
        $user = new User();

        $finalPassword = $user->generatePassword($password);

        $user->password = $finalPassword;
        $user->id = $id;

        $userDAO->changePassword($user);

    } else {
        $message->setMessage("As senhas não são iguais!", "error", "back");
    }
} else {
    $message->setMessage("Informações inválidas!", "error", "index.php");
}