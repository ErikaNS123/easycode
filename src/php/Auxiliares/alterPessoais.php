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

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $celular = explode(' ',$_POST['celular']);
    $celular = preg_replace('/[+() -]/','',implode('+',\array_splice($celular,1,3)));

    $emails = $sql -> query("SELECT * FROM aluno WHERE email = '$email' AND matricula != '$matricula'");

    if (mysqli_num_rows($emails) != 0) {
        echo "<h1>Uma conta já foi criada neste e-mail</h1>";
        header("Refresh: 2; ../perfil.php");
    }
    
    $sql -> query(
        "UPDATE aluno SET
            `nome` = '$nome',
            `telefone` = '$celular',
            `email` = '$email'
        WHERE matricula = '$matricula'");

    echo "<h1>Alterações Realizadas com sucesso!</h1>";
    header("Refresh: 2; ../perfil.php");

?>