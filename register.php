<?php

//  Страница регистрации нового пользователя
//  Соединяемся с БД

$link = mysqli_connect("127.0.0.1", "root", "", "testtable");

if(isset($_POST['submit'])){

    $err = [];

    //  Проверяем имя пользователя
    if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST['name'])){
        $err[] = "Имя может состоять только из букв английского алфавита и цифр";
    }

    if(strlen($_POST['name']) < 3 or strlen($_POST['name']) > 30){
        $err[] = "Имя должно быть не меньше 3-х символов и не больше 30";
    }

    //  Проверяем, существует ли уже пользователь с таким email
    $query = mysqli_query($link, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($link, $_POST['email']) . "'");

    if(mysqli_num_rows($query) > 0){
        $err[] = "Пользователь с таким email уже существует в базе данных";
    }

    //  Проверяем корректность email
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $err[] = "Некорректный email адрес";
    }

    //  Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0){

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = md5(md5(trim($_POST['password'])));

        // Устанавливаем значения для created_at и updated_at
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");

        mysqli_query($link, "INSERT INTO users (name, email, password, created_at, updated_at) VALUES ('$name', '$email', '$password', '$created_at', '$updated_at')");
        header("Location: login.php");  exit();

    } else {
        print "<b>При регистрации возникли следующие ошибки: </b><br>";
        foreach($err as $error){
            print $error . "<br>";
        }
    }

}

?>

<form method="POST">
    Имя: <input name="name" type="text" required><br>
    Email: <input name="email" type="email" required><br>
    Пароль: <input name="password" type="password" required><br>
    <input name="submit" type="submit" value="Зарегистрироваться">
</form>
