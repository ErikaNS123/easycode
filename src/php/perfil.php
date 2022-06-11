<?php

    include 'Auxiliares/connect.php';

    if (!isset($_SESSION)) {
        session_start();
    }
    
    // !Testa se esta logado ou não
    if (isset($_SESSION['matricula'])) {
        $matricula = $_SESSION['matricula'];
        $usuario = substr($matricula,0,1) == 0 ? 'aluno' : 'professor';
    }
    else{
        header('Location: cadastro_login.php');
    }

    if (isset($_GET['secoes'])) {
        echo "<input hidden value='$_GET[secoes]' id='secoes'></input>";
    }

    clearstatcache();

    $dadosUsuario = $sql -> query("SELECT * FROM $usuario WHERE matricula = '$matricula'");
    
    if ($usuario == 'aluno') {
        while ($dados = mysqli_fetch_array($dadosUsuario)) {
            $idAluno = $dados['id'];
            $nome = $dados['nome'];
            $avatar = $dados['avatar'];
            $email = $dados['email'];
            $celular = $dados['telefone'];
            $linkedin = $dados['linkedin'];
            $github = $dados['github'];
            $link = $dados['link_personalizado'];
        }

        $cursosI = $sql -> query(
            "SELECT 
                curso.linguagem, cert.fase,curso.id
            FROM certificado AS cert 
            INNER JOIN curso ON cert.id_curso = curso.id
            WHERE id_aluno = '$idAluno' AND `status` = 'NÃO TERMINADO'
            ORDER BY cert.id"
        );

        $nãoCursosI = false;
        if (mysqli_num_rows($cursosI) == 0) {
            $nãoCursosI = true;
        }

        $certificados = $sql -> query(
            "SELECT 
                curso.linguagem,curso.campo,curso.logo, curso.id,
                cert.data_inicio,cert.data_fim,
                prof.nome AS nome_prof
            FROM certificado AS cert 
            INNER JOIN curso ON cert.id_curso = curso.id
            INNER JOIN professor AS prof ON cert.id_responsavel = prof.id
            WHERE id_aluno = '$idAluno' AND `status` = 'TERMINADO'
            ORDER BY cert.id"
        );
    
        $nãoCertificado = false;
        if (mysqli_num_rows($certificados) == 0) {
            $nãoCertificado = true;
        }
    } else {
        while ($dados = mysqli_fetch_array($dadosUsuario)) {
            $idProfessor = $dados['id'];
            $nome = $dados['nome'];
            $avatar = $dados['avatar'];
            $email = $dados['email'];
            $celular = $dados['telefone'];
        }

        $cursosM = $sql -> query(
            "SELECT 
                curso.linguagem
            FROM curso
            INNER JOIN professor AS prof ON curso.id_responsavel = prof.id
            WHERE curso.id_responsavel = '$idProfessor'
            ORDER BY curso.linguagem"
        );
    }

    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <!--link do icon-->
        <meta charset='UTF-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$nome</title>
        <link rel='icon' type='imagem/png' href='../assets/img/logoEASYCODE.png'>

        <link rel='stylesheet' href='../assets/css/perfil.css'>
        <link rel='stylesheet' href='../assets/css/style.css'>
        <link rel='stylesheet' href='../assets/css/inputs.css'>
        <link rel='stylesheet' href='../assets/css/sidebar.css'>

        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css'>

        <link rel='stylesheet' href='../assets/css/pagecursos.css'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css'>
    </head>
    ";

    echo
    "
    <body onload='carousel();inicio();'>
        <div>
            <!-- Abrir menu -->
            <a class='botao-hamburguer abrir-menu' href='#' role='button'>
                <i class='bi bi-list'></i> 
            </a>
        </div>
        <!-- Sidebar -->
        <nav class='sidebar'>
            <!-- Fechar Menu-->
            <div class='fechar-x'>
                <i class='bi bi-x'></i>
            </div>
            <div class='d-flex justify-content-center align-items-center flex-column h-100 w-100'>
                <div class='profile'>
                    <img src='../assets/img/Avatares/$avatar' class='avatar'>
                    <h3>$nome</h3>
                    <p class='text-uppercase'>$matricula</p>
                </div>
    ";
    echo <<<opcoes
                <ul class='menu-elements w-100'>
                    <li class='active'>
                        <label for='home' onclick='opcoes("home")'>
                            <span>
                                <i class='bi bi-house-door-fill'></i>
                            </span>
                            <span>Home</span>
                        </label>
                        <input type='radio' name='opcoes' id='home' class='opcoes'>
                    </li>
opcoes;
    if ($usuario == 'aluno') {
        echo <<<certificadoAlunos
                    <li>
                        <label for='certificados' onclick='opcoes("certificados")'>
                            <span>
                                <i class="bi bi-award-fill"></i> 
                            </span>
                            <span>Certificados</span>
                        </label>
                        <input type='radio' name='opcoes' id='certificados' class='opcoes'>
                    </li>
certificadoAlunos;
    }
    else{
        echo <<<cadastrarCursos
                    <li>
                        <label for='cadastrarCursos' onclick='opcoes("cadastrarCursos")'>
                            <span>
                                <i class="bi bi-journal-plus"></i> 
                            </span>
                            <span>Cadastrar Cursos</span>
                        </label>
                        <input type='radio' name='opcoes' id='cadastrarCursos' class='opcoes'>
                    </li>
cadastrarCursos;
    }
    
    echo <<<opcoes
                    <li>
                        <label for='dPessoais' onclick='opcoes("dPessoais")'>
                            <span>
                                <i class="bi bi-person-lines-fill"></i>
                            </span>
                            <span>Dados Pessoais</span>
                        </label>
                        <input type='radio' name='opcoes' id='dPessoais' class='opcoes'>
                    </li>
opcoes;

    if ($usuario == 'aluno') {
        echo <<<dProfissionaisAlunos
                    <li>
                        <label for='dProfissionais' onclick='opcoes("dProfissionais")'>
                            <span>
                                <i class="bi bi-person-lines-fill"></i>
                            </span>
                            <span>Dados Profissionais</span>
                        </label>
                        <input type='radio' name='opcoes' id='dProfissionais' class='opcoes'>
                    </li>
dProfissionaisAlunos;                    
    }
    echo <<<opcoes
                    <li>
                        <label for='alterSenha' onclick='opcoes("alterSenha")'>
                            <span>
                                <i class="bi bi-shield-lock-fill"></i> 
                            </span>
                            <span>Alterar Senha</span>
                        </label>
                        <input type='radio' name='opcoes' id='alterSenha' class='opcoes'>
                    </li>

                    <li>
                        <a href='Auxiliares/sair.php'>
                            <label>
                                <span>
                                    <i class="bi bi-box-arrow-right"></i>
                                </span>
                                <span>Sair</span>
                            </label>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <section class='secao secaoAp' id='secao_home'>
opcoes;

    if ($usuario == 'aluno') {
        echo
        "
            <div class='container d-flex align-center justify-content-center nonSelect h-100'>
                <div class='h-75 w-100'>
                    <div class='w-100 h-50'>
                        <div class='w-100 h-100 row'>
                            <div class='m-2 h-100 border rounded d-flex justify-content-center align-center flex-column home'>
        ";
        if (!$nãoCursosI) {
            echo
            "
                                <h1 class='text-center color'>Continuar</h1>
                                <div class='d-flex justify-content-center align-items-center h-100 owl-carousel w-100'>
            ";
            while ($curso = mysqli_fetch_array($cursosI)) {
                
                echo
                "
                                <div class='d-flex flex-column col-md-3 col-sm-5 d-inline-block fundocard carouselA w-100'>
                                    <div class='d-flex justify-content-center align-items-center h-100'>
                                        <h5 class='text-center color m-0'>$curso[linguagem]</h5>
                                    </div>
                                    <a href='template_cursos.php?curso=$curso[id]'>
                                        <div class='d-flex h-50 m-3 home'>
                                            <div class='d-flex justify-content-center align-items-center col-10 p-1'>
                                                <p class='text-center overflow-hidden m-0 text-light p-1'>$curso[fase]</p>
                                            </div>
                                            <div class='d-flex justify-content-center align-items-center buscaS col-2'>    
                                                    <img src='../assets/img/proximo.png' width='100%'>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                
                ";
            }
        } else {
            echo 
            "
                                <div class='d-flex justify-content-center align-items-center h-100 w-100'>
                                    <div class='d-flex justify-content-center m-5 w-100'>
                                        <div class='w-100'>
                                            <h1 class='Josefinfont color text-center'>Ainda não há nada aqui...</h1>
                                        </div>
                                    </div>
            ";
        }

        echo
        "
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='h-50 d-flex justify-content-between align-items-center flex-wrap'>
                        <div class='col-md-6 col-12 h-100'>
                            <div class='my-3 h-75 rounded px-5'>
                                <div class='d-flex justify-content-center align-items-center flex-column h-100 home'>
                                    <div class='d-flex d-inline-block fundocard m-2 camposM'>
                                        <div class='col-lg-5 d-none d-lg-block'>
                                            <div class='d-flex justify-content-end align-items-center h-100'>
                                                <p class='sizeF text-center m-0'>mais cursos &nbsp;</p>
                                            </div>
                                        </div>
                                        <div class='col-lg-5 col-md-10 col-sm-10 col-10 d-flex justify-content-start align-items-center'>
                                            <h5 class='sizeF text-center m-0 color'><strong>FRONT-END</strong></h5>
                                        </div>
                                        <div class='d-flex justify-content-center align-items-center col-md-2 buscaS'>
                                            <a href='pagecursos.php#frontend'>   
                                                <img src='../assets/img/proximo.png' width='80em'>
                                            </a>
                                        </div>
                                    </div>
                                    <div class='d-flex d-inline-block fundocard m-2 camposM'>
                                        <div class='col-lg-5 d-none d-lg-block'>
                                            <div class='d-flex justify-content-end align-items-center h-100'>
                                                <p class='sizeF text-center m-0'>mais cursos &nbsp;</p>
                                            </div>
                                        </div>
                                        <div class='col-lg-5 col-md-10 col-sm-10 col-10 d-flex justify-content-start align-items-center'>
                                            <h5 class='sizeF text-center m-0 color'><strong>BACK-END</strong></h5>
                                        </div>
                                        <div class='d-flex justify-content-center align-items-center col-md-2 buscaS'>
                                            <a href='pagecursos.php#backend'>   
                                                <img src='../assets/img/proximo.png' width='80em'>
                                            </a>
                                        </div>
                                    </div>
                                    <div class='d-flex d-inline-block fundocard m-2 camposM'>
                                        <div class='col-lg-5 d-none d-lg-block'>
                                            <div class='d-flex justify-content-end align-items-center h-100'>
                                                <p class='sizeF text-center m-0'>mais cursos &nbsp;</p>
                                            </div>
                                        </div>
                                        <div class='col-lg-5 col-md-10 col-sm-10 col-10 d-flex justify-content-start align-items-center'>
                                            <h5 class='sizeF text-center m-0 color'><strong>DATABASE</strong></h5>
                                        </div>
                                        <div class='d-flex justify-content-center align-items-center col-md-2 buscaS'>
                                            <a href='pagecursos.php#database'>
                                                <img src='../assets/img/proximo.png' width='80em'>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-6 col-12 h-100'>
                            <div class='my-3 h-75 rounded px-5'>
                                <div class='d-flex justify-content-center align-items-center flex-column h-100 home'>
                                    <div class='h-100 d-flex justify-content-center align-items-center flex-column color'>
                                        <h2>Alguma dúvida?</h2>
                                        <h4>Fale com a gente</h4>
                                        <a href='sobrenos.php'>
                                            <button class='btn btn-outline-secondary fundocard color'>CONTATOS</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";
    } else{
        //!Professor
        echo
        "
            <div class='container d-flex align-center justify-content-center nonSelect h-100'>
                <div class='h-75 w-100'>
                    <div class='w-100 h-50'>
                        <div class='w-100 h-100 row'>
                            <div class='m-2 h-100 border rounded d-flex justify-content-center align-center flex-column home'>
        ";

        if (mysqli_num_rows($cursosM) != 0) {
            echo "<div class='d-flex justify-content-center align-items-center h-100 owl-carousel w-100' h-100>";

            while ($cursoP = mysqli_fetch_array($cursosM)) {
                $matriculados = $sql -> query(
                    "SELECT 
                        curso.id
                    FROM curso
                    INNER JOIN certificado AS cert ON cert.id_curso = curso.id
                    WHERE cert.id_responsavel = '$idProfessor' 
                        AND curso.linguagem = '$cursoP[linguagem]'
                    ORDER BY cert.id"
                );
                $terminados = $sql -> query(
                    "SELECT 
                        curso.id
                    FROM curso
                    INNER JOIN certificado AS cert ON cert.id_curso = curso.id
                    WHERE cert.id_responsavel = '$idProfessor' 
                        AND curso.linguagem = '$cursoP[linguagem]' 
                        AND cert.`status` = 'TERMINADO'
                    ORDER BY cert.id"
                );
                $matriculados = mysqli_num_rows($matriculados);
                $terminados = mysqli_num_rows($terminados);

                echo
                    "
                                        <div class='d-flex flex-column col-md-3 col-sm-5 d-inline-block fundocard carouselP w-100'>
                                            <div class='d-flex justify-content-center align-items-center h-50'>
                                                <h5 class='text-center color m-0 text-uppercase'>$cursoP[linguagem]</h5>
                                            </div>
                                            <div class='d-flex align-items-center justify-content-center h-100'>
                                                <div class='d-flex flex-column align-items-end justify-content-center col-7'>
                                                    <p class='m-1'><strong>Visualizações: </strong></p>
                                                    <p class='m-1'><strong>Matriculados: </strong></p>
                                                    <p class='m-1'><strong>Concluidos: </strong></p>
                                                </div>
                                                <div class='d-flex flex-column align-items-center justify-content-center col-5'>
                                                    <p class='m-1 color'>15</p>
                                                    <p class='m-1 color'>$matriculados</p>
                                                    <p class='m-1 color'>$terminados</p>
                                                </div>
                                            </div>
                                            <a href='template_cursos.php?curso=005' class='h-25'>
                                                <div class='d-flex h-50 m-3 home'>
                                                    <div class='d-flex justify-content-center align-items-center col-10 p-1'>
                                                        <p class='text-center text-wrap m-0 text-light p-1' style='font-size:0.9em;'>
                                                            Ir para a página do curso
                                                        </p>
                                                    </div>
                                                    <div class='d-flex justify-content-center align-items-center buscaS col-2'>    
                                                        <img src='../assets/img/proximo.png' width='100%'>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
    
                ";
            }
        } else {
            echo
            "
                                <div class='d-flex justify-content-center align-items-center h-100 w-100'>
                                    <div class='d-flex justify-content-center m-5 w-100'>
                                        <div class='w-100'>
                                            <h1 class='Josefinfont color text-center'>Ainda não há nada aqui...</h1>
                                        </div>
                                    </div>
            ";
        }
            echo
            "
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='h-50 d-flex justify-content-between align-items-center flex-wrap'>
                        <div class='col-md-6 col-12 h-100'>
                            <div class='my-3 h-75 rounded px-5'>
                                <div class='d-flex justify-content-center align-items-center flex-column h-100 home'>
                                    <div class='d-flex d-inline-block fundocard m-2 camposM'>
                                        <div class='col-lg-5 d-none d-lg-block'>
                                            <div class='d-flex justify-content-end align-items-center h-100'>
                                                <p class='sizeF text-center m-0'>mais cursos &nbsp;</p>
                                            </div>
                                        </div>
                                        <div class='col-lg-5 col-md-10 col-sm-10 col-10 d-flex justify-content-start align-items-center'>
                                            <h5 class='sizeF text-center m-0 color'><strong>FRONT-END</strong></h5>
                                        </div>
                                        <div class='d-flex justify-content-center align-items-center col-md-2 buscaS'>
                                            <a href='pagecursos.php#frontend'>   
                                                <img src='../assets/img/proximo.png' width='80em'>
                                            </a>
                                        </div>
                                    </div>
                                    <div class='d-flex d-inline-block fundocard m-2 camposM'>
                                        <div class='col-lg-5 d-none d-lg-block'>
                                            <div class='d-flex justify-content-end align-items-center h-100'>
                                                <p class='sizeF text-center m-0'>mais cursos &nbsp;</p>
                                            </div>
                                        </div>
                                        <div class='col-lg-5 col-md-10 col-sm-10 col-10 d-flex justify-content-start align-items-center'>
                                            <h5 class='sizeF text-center m-0 color'><strong>BACK-END</strong></h5>
                                        </div>
                                        <div class='d-flex justify-content-center align-items-center col-md-2 buscaS'>
                                            <a href='pagecursos.php#backend'>   
                                                <img src='../assets/img/proximo.png' width='80em'>
                                            </a>
                                        </div>
                                    </div>
                                    <div class='d-flex d-inline-block fundocard m-2 camposM'>
                                        <div class='col-lg-5 d-none d-lg-block'>
                                            <div class='d-flex justify-content-end align-items-center h-100'>
                                                <p class='sizeF text-center m-0'>mais cursos &nbsp;</p>
                                            </div>
                                        </div>
                                        <div class='col-lg-5 col-md-10 col-sm-10 col-10 d-flex justify-content-start align-items-center'>
                                            <h5 class='sizeF text-center m-0 color'><strong>DATABASE</strong></h5>
                                        </div>
                                        <div class='d-flex justify-content-center align-items-center col-md-2 buscaS'>
                                            <a href='pagecursos.php#database'>
                                                <img src='../assets/img/proximo.png' width='80em'>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-6 col-12 h-100'>
                            <div class='my-3 h-75 rounded px-5'>
                                <div class='d-flex justify-content-center align-items-center flex-column h-100 home'>
                                    <div class='h-100 d-flex justify-content-center align-items-center flex-column color'>
                                        <h2>Alguma dúvida?</h2>
                                        <h4>Fale com a gente</h4>
                                        <a href='sobrenos.php'>
                                            <button class='btn btn-outline-secondary fundocard color'>CONTATOS</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }

    echo
    "
        </section>
        <section class='secao secaoAp' id='secao_cadastrarCursos'>
            <h1 class='d-flex align-center justify-content-center'>MINISTRAR CURSO</h1>
            <form>
                <div class='form-group row'>
                    <label class='col-sm-2 col-form-label'>Qual curso deseja ministrar?</label>
                    <div class='col-sm-10'>
                        <select class='form-control'>
                            <option>opções automaticas</option>
                        </select>
                    </div>
                </div>
            </form>
            <div>
                <p>
                    Definição
                </p>
                <div>
                    Carga horária:
                    <input type='number'>
                    horas
                </div>
                <div>
                    Quantidade de fases:
                    <input type='number'>
                </div>
                <div class='form-group row'>
                    <label class='col-sm-2 col-form-label'>Conteudo ensinado:</label>
                    <div class='col-sm-8'>
                        <textarea class='form-control' rows='2' placeholder='Descreva em tópicos todo o conteúdo que será ensinado neste curso'></textarea>
                    </div>
                </div>
            </div>
            <div>
                <p class='btn btn-outline-secondary bg-color text-light bold'>
                    <strong>ADICIONAR SEÇÃO</strong>
                    <i class='bi bi-plus-square'></i>
                </p>
            </div>
            <div class='d-flex justify-content-center'>
                <input type='submit' value='SALVAR' class='btn btn-outline-secondary bg-color text-light'>
                <input type='submit' value='PUBLICAR' class='btn btn-outline-secondary bg-color text-light'>
            </div>
        </section>
    ";


    echo
    "  
        <section class='secao h-100' id='secao_certificados'>
            <div class='container d-flex align-center justify-content-center nonSelect h-100'>
    ";

    if ($usuario == 'aluno') {

        if ($nãoCertificado) {
            echo 
            "
                <div class='d-flex justify-content-center m-5 p-5'>
                    <h1 class='Josefinfont'>Ainda não há nada aqui...</h1>
                </div>
            ";
        } else {
            while ($certificado = mysqli_fetch_array($certificados)) {
                $data_ini = implode('/',array_reverse(explode('-',$certificado['data_inicio'])));
                $data_fim = implode('/',array_reverse(explode('-',$certificado['data_fim'])));
        
                echo 
                "
                    <div class='cardCertificado'>
                        <div class='before'>
                            <div class='titulo'>
                                <h3>$certificado[linguagem]</h3>
                            </div>
                            <div class='logo_curso'>
                                <img src='../assets/img/logo_cursos/$certificado[logo]' width='150px'>
                            </div>
                        </div>
                        <div class='content'>
                            <h3>$certificado[linguagem]</h3>
                            <br>
                            <p><strong>Data Final:</strong> $data_fim</p>
                            <p><strong>Professor:</strong> $certificado[nome_prof]</p>
                            <br>
                            <div>
                                <img src='../assets/img/logo_cursos/menores/$certificado[logo]' width='90px'>
                            </div>
                            <a href='Auxiliares/pdf-certificado.php?curso=$certificado[id]'>Baixe o PDF</a>
                        </div>
                    </div>
                ";
            }
        }
    }

    echo
    "
            </div>  
        </section>
        <section class='secao' id='secao_dPessoais'>
            <div class='d-flex justify-content-center align-items-center h-100'>
                <div class='box-form'>
                    <div class='d-flex flex-wrap justify-content-center align-items-center h-100 w-100'>
                        <form action='Auxiliares/alterAvatar.php' class='col-md-5 col-sm-10 d-flex justify-content-center align-items-center' method='post' id='form-avatar' enctype='multipart/form-data'>
                            <div class='d-flex justify-content-center align-items-center flex-column h-100 w-50'>
                                <label for='avatar'>
                                    <img src='../assets/img/Avatares/$avatar' class='avatar' width='150em'>
                                </label>
                                <input type='file' accept='image/*' id='avatar' name='avatar' onChange='submitForm()'>
                                <br>
                                <div class='d-flex justify-content-center'>
                                    <h3 class='Josefinfont text-color'>Matricula: $matricula</h3>
                                </div>
                            </div>
                        </form>
                        <form action='Auxiliares/alterPessoais.php' method='post' class='formulario flex-column form-alt col-md-5 col-sm-10 d-flex justify-content-center align-items-center'>
                            <div class='d-flex w-100'>
                                <div class='form-group mb-4 w-100'>
                                    <div class='input-container'>
                                        <label for='nome'><strong>Nome</strong></label>
                                        <input id='nome' class='input' name='nome' placeholder='#' type='text' required
                                        data-tipo='nome' value='$nome'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='d-flex w-100'>
                                <div class='form-group mb-4 w-100'>
                                    <div class='input-container'>
                                        <label for='email'><strong>E-mail</strong></label>
                                        <input id='email' class='input' name='email' placeholder='#' type='email' required
                                        data-tipo='email' value='$email'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='d-flex w-100'>
                                <div class='form-group mb-4 w-100'>
                                    <div class='input-container'>
                                        <label for='celular'><strong>Telefone</strong></label>
                                        <input id='celular' class='input' name='celular' placeholder='#' type='text' required
                                        data-tipo='celular' value='$celular'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='d-flex justify-content-center'>
                                <input type='submit' value='SALVAR ALTERAÇÕES' class='btn btn-outline-secondary bg-color text-light'>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <section class='secao' id='secao_dProfissionais'>
            <div class='d-flex justify-content-center align-items-center h-100'>
                <div class='box-form'>
                    <div class='d-flex justify-content-center align-items-center flex-column w-100 h-100'>
                        <form action='Auxiliares/alterProfissionais.php' method='post' class='formulario flex flex--coluna form-alt w-50'>
                            <div class='w-100'>
                                <div class='form-group'>
                                    <div class='input-container'>
                                        <label for='linkedin'><strong>Linkedin</strong></label>
                                        <input id='linkedin' class='input' name='linkedin' placeholder='#' type='url' data-tipo='link' value='$linkedin'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='w-100'>
                                <div class='form-group'>
                                    <div class='input-container'>
                                        <label for='github'><strong>GitHub</strong></label>
                                        <input id='github' class='input' name='github' placeholder='#' type='url' data-tipo='link' value='$github'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='w-100'>
                                <div class='form-group'>
                                    <div class='input-container'>
                                        <label for='link'><strong>Link Pessoal</strong></label>
                                        <input id='link' class='input' name='link' placeholder='#' type='url' data-tipo='link' value='$link'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='d-flex justify-content-center'>
                                <input type='submit' value='SALVAR ALTERAÇÕES' class='btn btn-outline-secondary bg-color text-light'>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <section class='secao' id='secao_alterSenha'>
            <div class='d-flex justify-content-center align-items-center h-100'>
                <div class='box-form'>
                    <div class='d-flex justify-content-center align-items-center flex-column w-100 h-100'>
                        <form action='Auxiliares/alterSenha.php' method='post' class='formulario flex flex--coluna form-alt w-50'>
                            <div class='w-100'>
                                <div class='form-group'>
                                    <div class='input-container'>
                                        <label for='senhaAnt'><strong>Senha antiga</strong></label>
                                        <input id='senhaAnt' class='input' type='password' required data-tipo='senhaCad'  name='senhaAnt' pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?!.*[ \\\/!@#$%^&*_=+-]).{6,12}$'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='w-100'>
                                <div class='form-group'>
                                    <div class='input-container'>
                                        <label for='senha'><strong>Nova senha</strong></label>
                                        <input id='senha' class='input' type='password' required data-tipo='senhaCad'  name='senha' pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?!.*[ \\\/!@#$%^&*_=+-]).{6,12}$'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='w-100'>
                                <div class='form-group'>
                                    <div class='input-container'>
                                        <label for='senhaNov'><strong>Repita a nova senha</strong></label>
                                        <input id='senhaNov' class='input' type='password' required data-tipo='senhaNov'  name='senhaNov' pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?!.*[ \\\/!@#$%^&*_=+-]).{6,12}$'>
                                        <span class='input-mensagem-erro'>Este campo não está valido</span>
                                    </div>
                                </div>
                            </div>
                            <div class='w-50'>
                                <a href='resgSenha.php' class='color'>
                                    <strong>Esqueci a senha</strong>
                                </a>
                            </div>
                            <br>
                            <div class='d-flex justify-content-center'>
                                <input type='submit' value='SALVAR ALTERAÇÕES' class='btn btn-outline-secondary bg-color text-light'>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <script src='../assets/js/carouselPerfil.js'></script>
        <script src='../assets/js/libs/jquery.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js'></script>
        <script src='../assets/js/app.js' type='module'></script>
        <script src='../assets/js/libs/jquery.mask.js'></script>
        <script src='../assets/js/mascara.js'></script>
        <script src='../assets/js/alterAvatar.js'></script>
        <script src='../assets/js/apresentacaoPerfil.js'></script>
        <script src='../assets/js/sidebar.js'></script>
    </body>
    </html>
    ";
?>