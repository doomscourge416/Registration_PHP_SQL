<?php

//  Страница авторизации
//  Функция для генерации случайной строки

function generateCode($length = 6){
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $codelength = strlen($chars) - 1;

    while (strlen($code) < $length){
        $code . = $chars[mt_rand(0, $codelength)];
    }

    return $code;
}

//  Соединяемся с БД
$link = mysqli_connect("localhost", "mysql_user", "mysql_password", "testtable");
if(isset($_POST['submit'])){

    //  Вытаскиваем из БД запись, у которой логин равняется введенному
    $query = mysqli_query($link, "SELECT user_id, user_password FROM users WHERE user_login = '" . mysqli_real_escape_string($link, $_POST['login']) . "' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
    //  Сравниваем пароли
    if($data['user_password'] === md5(md5($_POST['password']))){

        //  Генерируем случаное число и шифруем его
        $hash = md5(generateCode(10));

        if(!empty($_POST['not_attach_ip'])){

            //  Если пользователь выбрал привязку к IP
            //  Переводим IP в строку
            $insip = ", user_ip=INET_ATON('" . $_SERVER['REMOTE_ADDR'] . "')";

        }

        //  Записываем в БД новый хеш авторизации и IP
        mysqli_query($link, "UPDATE users SET user_hash='" . $hash . "'" . $insip . "WHERE user_id='" . $data['user_id'] . "'");

        //  Ставим куки
        setcookie("id", $data['user_id'], time()+60*60*24*30, "/");
        setcookie("hash", $hash, time()+60*60*24*30, "/");

        //  Переадресовываем браузер на страницу проверки скрипта
        header("Location: check.php"); exit();

    } else {
        print "Вы ввели неправильный логин или пароль";
    }

}

?>

<form method="POST">
    Логин:  <input name="login" type="text" required><br>
    Пароль: <input name="password" type="password" required><br>
    Не прикреплять к IP (Небезопасно)   <input type="checkbox" name="not_attach_ip"><br>
    <input name="submit" type="submit" value="Войти"><br>
</form>