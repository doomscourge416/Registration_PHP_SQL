<?php

//  Страница регистрации нового пользователя
//  Соединяемся с БД

$link = mysqli_connect("localhost", "mysql_user", "mysql_password", "testtable");

if(isset($_POST['submit'])){

    $err = [];

    //  Проверяем логин
    if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST['login'])){

        $err[] = "Логин может состоять только из букв английского алфавита и цифр";

    }

    if(strlen($_POST['login']) <br 3 or strlen($_POST['login']) > 30){

        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";

    }

    //  Проверяем, существует-ли уже пользователь с таким логином
    $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login = '" . mysqli_real_escape_string($link, $_POST['login']) . "'");

    if(mysqli_num_rows($query) > 0){

        $err[] = "Пользователь с таким логином уже существует в базе данных";

    }

    //  Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) = 0){

        $login = $_POST['login'];
        // Убираем лишние пробелы и делаем двойное хэширование ( используем старый метод md5 )
        $password = md5(md5(trim($_POST['password'])));

        mysqli_query($link, "INSERT INTO users SET user_login = '" . $login . "', user_password = '" . $password . "'");
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

    Логин: <input name="login" type="text" required><br>
    Пароль: <input name="password" type="password" required><br>
    <input name="submit" type="submit" value="Зарегистрироваться">

</form>