<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if($_SESSION['tipo'] != 2){
    header("Location: cuenta");
    exit;
}

// Base de Datos
include_once('clases/conexion.php');

date_default_timezone_set('America/Caracas');
$date = date('Y-m-d');

$select = $cnx->prepare('SELECT * FROM usuario WHERE estado_user != -1 ORDER BY nombre DESC');
$select->execute();


?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/reportes.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Generar Reportes</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
            include_once 'partials/header.php';
            include_once 'partials/nav.php';
            ?>

            <div class="post"></div>

            <section class="Cuerpo">
                <form action="controladores/hacerReporte.php" method="post" id="generador_reportes" target="_blank">
                    <fieldset class="fieldset-generar-reporte" id="seleccionar-tipo-reporte">
                        <h1>Seleccione el tipo de Reporte:</h1>
                        <label for="tipo_reporte1" class="seleccion" title="Registro de Noticias en el Sistema.">
                            <input type="radio" name="tipo_reporte" id="tipo_reporte1" value="Noticia" class="tipo-reporte-opcion">
                            <span>Noticia</span>
                        </label>
                        <label for="tipo_reporte2" class="seleccion" title="Registro de Documentos en el Sistema.">
                            <input type="radio" name="tipo_reporte" id="tipo_reporte2" value="Documento" class="tipo-reporte-opcion">
                            <span>Documento</span>
                        </label>
                        <label for="tipo_reporte3" class="seleccion" title="Registro de Solicitudes de Documentos en el Sistema.">
                            <input type="radio" name="tipo_reporte" id="tipo_reporte3" value="Solicitud" class="tipo-reporte-opcion">
                            <span>Solicitud</span>
                        </label>
                        <label for="tipo_reporte4" class="seleccion" title="Registro de Usuarios en el Sistema.">
                            <input type="radio" name="tipo_reporte" id="tipo_reporte4" value="Usuario" class="tipo-reporte-opcion">
                            <span>Usuario</span>
                        </label>
                        <label for="tipo_reporte5" class="seleccion" title="Registro de Acciones en el Portal Web.">
                            <input type="radio" name="tipo_reporte" id="tipo_reporte5" value="Accion" class="tipo-reporte-opcion">
                            <span>Acciones</span>
                        </label>
                        <label class="hidden"></label>
                    </fieldset>

                    <fieldset class="fieldset-generar-reporte hidden extra" id="seleccionar-periodo">
                        <h1>Periodo de Tiempo</h1>
                        <select name="periodo_tiempo" id="seleccion-tiempo">
                            <option value="todos">Todos</option>
                            <option value="hoy">Hoy</option>
                            <option value="semana">Últ. semana</option>
                            <option value="mes">Últ. mes</option>
                            <option value="trimestre">Últ. trimestre</option>
                            <option value="semestre">Últ. semestre</option>
                            <option value="anno">Últ. año</option>
                            <option value="personalizado">Personalizado</option>
                        </select>

                        <div id="seleccion-fecha-personalizada" class="blocked">
                            <h2>Inicio:</h2>
                            <input type="date" name="inicio_periodo" id="fecha-periodo-inicio" value="<?= $date ?>" readonly>
                            <h2>Fin:</h2>
                            <input type="date" name="final_periodo" id="fecha-periodo-final" value="<?= $date ?>" readonly>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-generar-reporte hidden extra" id="seleccionar-usuario">
                        <h1>Usuario</h1>
                        <?php 
                        $usuario = $cnx->prepare("SELECT nombre, apellido, id_user FROM usuario ORDER BY tipo DESC");
                        $usuario->execute();
                        $usuario = $usuario->fetchAll(PDO::FETCH_ASSOC);

                        $visitante = $cnx->prepare("SELECT nombre, apellido, id_user FROM usuario WHERE tipo = 0");
                        $visitante->execute();
                        $visitante = $visitante->fetchAll(PDO::FETCH_ASSOC);

                        $admin = $cnx->prepare("SELECT nombre, apellido, id_user FROM usuario WHERE tipo = 1");
                        $admin->execute();
                        $admin = $admin->fetchAll(PDO::FETCH_ASSOC);

                        $mod = $cnx->prepare("SELECT nombre, apellido, id_user FROM usuario WHERE tipo = 2");
                        $mod->execute();
                        $mod = $mod->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <script>
                            var usuarios = [];
                            var visitantes = [];
                            var admins = [];
                            var mods = [];

                            <?php 
                            foreach ($usuario as $i => $user) { ?>
                                usuarios[<?= $i ?>] = <?= json_encode($user) ?>;
                                <?php 
                            }

                            foreach ($visitante as $i => $user) { ?>
                                visitantes[<?= $i ?>] = <?= json_encode($user) ?>;
                                <?php 
                            }

                            foreach ($admin as $i => $user) { ?>
                                admins[<?= $i ?>] = <?= json_encode($user) ?>;
                                <?php 
                            }

                            foreach ($mod as $i => $user) { ?>
                                mods[<?= $i ?>] = <?= json_encode($user) ?>;
                                <?php 
                            } 
                            ?>
                        </script>

                        <div id="filtro-usuario">
                            <h2>Filtrar Por</h2>
                            <label for="visitante">
                                <input type="checkbox" name="tipo_usuario[]" value="0" id="visitante" checked>
                                <span>Visitante</span>
                            </label>
                            <label for="admin">
                                <input type="checkbox" name="tipo_usuario[]" value="1" id="admin" checked>
                                <span>Administrador</span>
                            </label>
                            <label for="mod">
                                <input type="checkbox" name="tipo_usuario[]" value="2" id="mod" checked>
                                <span>Moderador</span>
                            </label>
                        </div>

                        <div id="seleccion-usuario">
                            <label for="0">
                                <input type="checkbox" id="0" name="id_user[]" value="0" class="seleccion-id-user" checked>
                                <span>Todos</span>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-generar-reporte hidden extra" id="reporte-documento">
                        <h1>Opciones de Documento</h1>
                        <div id="filtrar-documento-autor">
                            <h2>Filtrar por Autor</h2>
                            <div id="seleccion-tipo-autores">
                                <label for="cualquier-autor" class="seleccion">
                                    <input type="radio" name="tipo_autor" id="cualquier-autor" value="0" checked>
                                    <span>Todos los autores</span>
                                </label>
                                <label for="personalizar-autor" class="seleccion">
                                    <input type="radio" name="tipo_autor" id="personalizar-autor" value="1">
                                    <span>Personalizados(as)</span>
                                </label>
                            </div>
                            <div id="seleccion-autor-personalizado" class="blocked"> 
                                <?php
                                $autores = $cnx->prepare("SELECT a.*, COUNT(ad.id_autor) AS cant_libros FROM autor a JOIN autor_documento ad ON a.id_autor = ad.id_autor GROUP BY a.id_autor ORDER BY a.name ASC");
                                $autores->execute();
                                $autores = $autores->fetchAll(PDO::FETCH_ASSOC);
                                foreach($autores as $autor){ ?>
                                    <label for="<?= $autor['id_autor'] ?>" class="seleccion-autor">
                                        <input type="checkbox" name="documento_autores[]" id="<?= $autor['id_autor'] ?>" value="<?= $autor['id_autor'] ?>">
                                        <span><?= "$autor[name] $autor[surname]: $autor[cant_libros]" ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>

                        <div id="filtrar-documento-palabraclave">
                            <h2>Filtrar por Palabra Clave</h2>
                            <div id="seleccion-tipo-kw">
                                <label for="cualquier-kw" class="seleccion">
                                    <input type="radio" name="tipo_kw" id="cualquier-kw" value="0" checked>
                                    <span>Todas las Palabras Clave</span>
                                </label>
                                <label for="personalizar-kw" class="seleccion">
                                    <input type="radio" name="tipo_kw" id="personalizar-kw" value="1">
                                    <span>Personalizadas</span>
                                </label>
                            </div>

                            <div id="seleccion-kw-personalizado" class="blocked">
                                <?php
                                $keywords = $cnx->prepare("SELECT k.*, COUNT(dk.id_kw) AS cant_libros FROM palabraclave k JOIN documento_palabraclave dk ON k.id_kw = dk.id_kw GROUP BY k.id_kw ORDER BY k.name_kw ASC");
                                $keywords->execute();
                                $keywords = $keywords->fetchAll(PDO::FETCH_ASSOC);
                                foreach($keywords as $kw){
                                    ?>
                                    <label for="<?= $kw['id_kw'] ?>" class="seleccion-autor">
                                        <input type="checkbox" name="documento_autores[]" id="<?= $kw['id_kw'] ?>" value="<?= $kw['id_kw'] ?>">
                                        <span><?= "$kw[name_kw]: $kw[cant_libros]" ?></span>
                                    </label>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </fieldset>

                    <div class="center">
                        <button type="submit" id="generar-el-reporte">Generar Reporte</button>
                    </div>
                </form>
            </section>
        </div>

        <script src="JavaScript/reportes.js"></script>
    </body>
</html>