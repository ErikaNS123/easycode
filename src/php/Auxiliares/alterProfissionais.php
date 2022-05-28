<!DOCTYPE html>
<html lang='pt-br'>
<head>
    <!--link do icon-->
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='../../assets/css/style.css'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
</head>
<body style="background-image: url(../../assets/img/bgimg.jpg);height:100vh">
    <div class="d-flex justify-content-center align-items-center h-100">
        <?php

            include 'connect.php';

            if (!isset($_SESSION)) {
                session_start();
            }
            
            // !Testa se esta logado ou não
            $logado = false;
            if (isset($_SESSION['matricula'])) {
                // !Testa se quem está logado é aluno ou professor
                $logado = true;
                $matricula = $_SESSION['matricula'];
            }
            else{
                header('Location: ../cadastro_login.html');
            }

            $linkedin = $_POST['linkedin'];
            $github = $_POST['github'];
            $link = $_POST['link'];

            $sql -> query(
                "UPDATE aluno SET
                    `linkedin` = '$linkedin',
                    `github` = '$github',
                    `link_personalizado` = '$link'
                WHERE matricula = '$matricula'");

            echo "<h1>Alterações Realizadas com sucesso!</h1>";
            header("Refresh: 2; ../perfil.php");

        ?>
    </div>
</body>
</html>