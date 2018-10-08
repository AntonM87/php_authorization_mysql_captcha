<?php


require_once 'lib/Reg_Auth.php';
require_once 'lib/Captcha_class.php';


define('LOCALHOST','localhost');
define('DATABASE_NAME','captcha');
define('LOGIN','root');
define('PASS','');
define('CHARSET','utf8');
define('SALT','§#*%*±@!±&$');

$login = $pass_second = $pass =false;

echo <<<_END
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Авторизация и Капча</title>
    <style>
    img{
    border: 1px solid black;
    }
</style>
</head>
<body>
<form action="index.php" method="post">
    <input type="text" value="$login" name="login" placeholder="Login" autocomplete="on"><br>
    <p>Введите пароль</p><input value="$pass" type="password" name="password" placeholder="password"><br>
    <p>Повторите пароль</p><input value="$pass_second" type="password" name="second_password" placeholder="password"><br>
    <div><img src="lib/captcha_result.php" alt=""></div>
    
    <div><p>Введите изображение с картинки</p></div>
    <input type="text" name="captcha">
    
    <p> </p><input type="submit" name="registration" value="Registration">
    <input type="submit" name="enter" value="Enter">
    <input type="submit" name="exit" value="Exit">
</form>
</body>
</html>
_END;

try {
    if (isset($_POST['registration'])) {

        $login = $_POST['login'];
        $pass = $_POST['password'];
        $captcha = $_POST['captcha'];
        $pass_second = $_POST['second_password'];

        $auth = new Reg_Auth($login,$pass);

        if ($auth->login_valid() && $auth->password_valid()) {

            if ($pass === $pass_second && Captcha_class::captcha_check($captcha)) {

                $pass = crypt($pass,SALT);

                $mysql_reg = new PDO('mysql:host=' . LOCALHOST . ';dbname=' . DATABASE_NAME . ';charset=' . CHARSET, LOGIN, PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $result_reg = $mysql_reg->query("select `login` from `captcha` where `login` = '$login'");
                $row_reg = $result_reg->fetch(PDO::FETCH_ASSOC);

                if ($row_reg['login'] == $login) {
                    throw new Exception("Данный логин ".$login." уже зарегестрирован");
                } else {
                    $query = ("insert into `captcha` (`login`,`pass`) values ('$login','$pass')");
                    if ($mysql_reg->query($query)) echo 'Пользователь "'.$login.'" зарегестрирован';
                }
            }else throw new Exception('Проверьте правильность ввода данных');
        }
        else throw new Exception('Проверьте правильность ввода данных');
    }
    if (isset($_POST['enter'])){

        $login = $_POST['login'];
        $pass = $_POST['password'];
        $pass_second = $_POST['second_password'];
        $captcha = $_POST['captcha'];

        $auth = new Reg_Auth($login,$pass);

        if ($auth->login_valid() && $auth->password_valid()){

            if ($pass === $pass_second) {

                echo '<---'.Captcha_class::captcha_check($captcha).'--->';

                $pass = crypt($pass, SALT);

                $mysql_ent = new PDO('mysql:host=' . LOCALHOST . ';dbname=' . DATABASE_NAME . ';charset=' . CHARSET, LOGIN, PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $result_ent = $mysql_ent->query("select `login`,`pass` from `captcha`.`captcha` where `login` = '$login' and `pass`='$pass'");
                $row_ent = $result_ent->fetch(PDO::FETCH_ASSOC);

                if ($row_ent['login'] === $login && $row_ent['pass'] === $pass){
                    echo "Добро пожаловать ".$login;
                }else throw new Exception('Проверьте логин и пароль');
            }else throw new Exception('Проверьте логин и пароль');
        }

    }
    if (isset($_POST['exit'])){
        $_SESSION = array();
    }
}catch (Exception $e){
    echo $e->getMessage();
}
?>

