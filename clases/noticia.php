<?php
class Noticia{
    private $id;
    private $titulo;
    private $cuerpo;
    private $fecha;
    private $hora;
    private $estado;
    private $autor; // Id de quien publica
    private $cnx;

    function __construct($titulo, $cuerpo, $autor, $cnx, $estado = null, $fecha = null, $hora = null, $id = null){
        $this->id       = $id;
        $this->titulo   = $titulo;
        $this->cuerpo   = $cuerpo;
        date_default_timezone_set('America/Caracas');
        $this->fecha    = $fecha    ?? date('Y-m-d');
        $this->hora     = $hora     ?? date('H:i');
        $this->estado   = $estado   ?? ($_SESSION['tipo'] == 2 ? 1 : 0);
        $this->autor    = $autor;
        $this->cnx      = $cnx;
    }

    // Métodos

    # **
    # ** Motrar Noticia
    # **

    function MostrarNoticia($comoModal = false){
        // Ocultar Noticia 
        if($this->estado != 1 || $this->estado == -1){
            if($_SESSION['sesionPortalWeb'] != $this->autor && $_SESSION['tipo'] != 2){
                return 0;
            }
        }

        // Cantidad de Comentarios
        $count_comments = $this->cnx->prepare("SELECT COUNT(*) FROM comentario WHERE id_new = :id_noticia");
        $count_comments->bindParam(':id_noticia', $this->id, PDO::PARAM_INT);
        $count_comments->execute();
        $count_comments = $count_comments->fetchColumn();

        // Detectar Multimedia
        $media = [];
        $sentencia = $this->cnx->prepare("SELECT COUNT(*) FROM multimedia WHERE id_new=:id");
        $sentencia->bindParam(':id', $this->id, PDO::PARAM_INT);
        $sentencia->execute();
        $row = $sentencia->fetchColumn();
        if($row > 0){
            $query = $this->cnx->prepare("SELECT media FROM multimedia WHERE id_new=:id");
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();
            
            $media = $query->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        // Mostrar Noticia 
        $prepare = $this->cnx->prepare("SELECT nombre, apellido FROM usuario WHERE id_user=:autor");
        $prepare->bindParam(':autor', $this->autor, PDO::PARAM_INT);
        $prepare->execute();
        $nombre = $prepare->fetch(PDO::FETCH_ASSOC);
        if(!empty($nombre)) $nombres = $nombre['nombre'] . " " . $nombre['apellido'];
        else $nombres = "Desconocido";

        if($this->estado == 2){
            $aux = 'oculta ';
        }else{
            $aux = 'visible ';
        }
        $clases = "noticia ". (($comoModal) ? "modal " : "") . $aux . (!empty($media) ? "media " : "");
        $id     = "modal" . $this->id;

        $hora = new DateTime($this->hora);

        ?>
        <div class="<?= $clases ?>" id="<?= $id ?>">
            <h3><?= $this->titulo ?></h3>
            <?php
            // Opciones 
            if(isset($_SESSION['sesionPortalWeb'])){ ?>
                <?php if($_SESSION['tipo'] == 2 || $_SESSION['sesionPortalWeb'] == $this->autor){ ?>
                    <div class="opciones">
                        <?php if($this->estado == 1 || $this->estado == 2){ ?>
                            <img src="images/ojo.svg" class="mostrar ojo" onclick="window.location.href='controladores/hidNew.php?new=<?= $this->id ?>&pag=<?= ($_GET['pag'] ?? 1) ?>'" title="Mostrar Noticia">
                            <img src="images/ojo-bizco.svg" class="ocultar ojo" onclick="window.location.href='controladores/showNew.php?new=<?= $this->id ?>&pag=<?= ($_GET['pag'] ?? 1) ?>'" title="Ocultar Noticia">
                        <?php }elseif($this->estado == 0){ ?>
                            <img src="images/verificacion-de-texto.svg" class="editar" title="Aprobar Noticia" onclick="window.location.href='controladores/approveNew.php?new=<?= $this->id ?>'">
                        <?php } ?>

                        <?php if($this->estado != 3){ ?>
                            <img src="images/lapiz.svg" class="editar" onclick="window.location.href='editNew-<?= $this->id ?>'" title="Editar Noticia">
                        <?php }else{ ?>
                            <img src="images/girar-en-reversa.svg" class="editar" title="Regresar Noticia" onclick="window.location.href='controladores/returnNew.php?new=<?= $this->id ?>'">
                        <?php } ?>
                        <img src="images/basura.svg" class="eliminar" onclick="window.location.href='controladores/deleteNew.php?new=<?= $this->id ?>'" title="Eliminar Noticia">
                    </div>
                <?php } ?>
                
                <div id="comments">
                    <a href="comentarios-<?= $this->id ."-". ($_GET['pag'] ?? 1) ?>">
                        <img src="images/comments.svg" alt="Comentar"> 
                        <?= $count_comments ? $count_comments : "" ?>
                    </a>
                </div>
            <?php } ?>
            <p><?= $this->cuerpo ?></p>
            <?php 
            if(!empty($media)){ ?>
                <div class="multimedia">
                    <?php 
                    foreach($media as $m){
                        if(pathinfo($m, PATHINFO_EXTENSION) == "mp4"){ ?>
                            <video src="<?= $m ?>" class="vid multimedia-content" id="<?= $m ?>" controls></video>
                            <?php 
                        }else{ ?>
                            <img src="<?= $m ?>" class="img multimedia-content" id="<?= $m ?>">
                            <?php 
                        }
                    } ?>
                </div>
                <?php 
            }

            if(isset($_SESSION['sesionPortalWeb'])){ ?>
                <h6>Publicado por <?= $nombres ?>: <?= (new DateTime($this->fecha))->format("d/m/Y") ?> | <?= $hora->format('H:i') ?></h6>
            <?php } ?>
        </div>
    <?php }

    # **
    # ** Fin Mostrar Noticia
    # **




    # Publicar en Base de Datos
    function DB_Post(){
        $sentencia = $this->cnx->prepare("INSERT INTO noticia(ttle_new, corp_new, fecha_new, hora_new, estado_new, id_user) VALUES (:ttle, :corp, :fecha, :hora, :visible, :autor)");
        $sentencia->bindParam(':ttle', $this->titulo, PDO::PARAM_STR);
        $sentencia->bindParam(':corp', $this->cuerpo, PDO::PARAM_STR);
        $sentencia->bindParam(':fecha', $this->fecha, PDO::PARAM_STR);
        $sentencia->bindParam(':hora', $this->hora, PDO::PARAM_STR);
        $sentencia->bindParam(':visible', $this->estado, PDO::PARAM_BOOL);
        $sentencia->bindParam(':autor', $this->autor, PDO::PARAM_INT);
        $sentencia->execute();
        $idNew = $this->cnx->lastInsertId();
        return $idNew;
    }


    # **
    # ** Hacer Resumen de Noticias.
    # **
    # **        # Esta función está diseñada para ser utilizada dentro de
                # una DataTable, por ende: Muestra la estructura de una
                # tabla, sin abrir ni cerrar su etiqueta principal «<table>».
    static function Resumen_Noticias($cnx){
        ?>
        <thead>
            <th>#N</th>
            <th>Título</th>
            <th>Cuerpo</th>
            <th>Autor</th>
            <th>Fecha</th>
            <th>Hora</th>
        </thead>

        <tbody>
            <?php
            $n = 0;
            $sentenciaSQL = $cnx->prepare("SELECT * FROM noticia WHERE estado_new != -1 ORDER BY id_new DESC");
            $sentenciaSQL->execute();
            while($row = $sentenciaSQL->fetch(PDO::FETCH_ASSOC)){
                $n++;
                // Usuario 
                $user = $cnx->prepare("SELECT usuario FROM usuario WHERE id_user = :id");
                $user->bindParam(':id', $row['id_user'], PDO::PARAM_INT);
                $user->execute();

                // Hora 
                $hora = new DateTime($row['hora_new']);
                ?>
                <tr>
                    <td><?= $n   ?></td>                    <!-- Número de fila -->
                    <td><?= $row['ttle_new'] ?></td>        <!-- Título -->
                    <td>                                    <!-- Cuerpo -->
                        <div class="seeMore" id="<?= $row['id_new'] ?>">Ver más...</div>
                        <div class="outModal" id="outModal<?= $row['id_new'] ?>"></div>
                        <?php
                        $noticia = new Noticia($row['ttle_new'], $row['corp_new'], $row['id_user'], $cnx, $row['estado_new'], $row['fecha_new'], $row['hora_new'], $row['id_new']);
                        $noticia->MostrarNoticia(true);
                        ?>
                    </td>
                    <td>@<?= $user->fetchColumn(); ?></td>  <!-- Autor -->
                    <td><?= $row['fecha_new'] ?></td>   <!-- Fecha -->
                    <td><?= $hora->format("H:i") ?></td><!-- Hora -->
                </tr>
            <?php
            }?>
        </tbody>
        <?php
    }
}

class Comentario{
    private $id_comment;
    private $id_new;
    private $id_user;
    private $comentario;
    private $cnx;
    private $posicion;
    private $fecha_comment;


    function __construct($new, $user, $text, $cnx, $pos = null, $date = null, $id = null){
        $this->id_new = $new;
        $this->id_user = $user;
        $this->comentario = $text;
        $this->cnx = $cnx;
        $this->posicion = $pos ?? 0;
        $this->id_comment = $id;
        date_default_timezone_set("America/Caracas");
        $this->fecha_comment = $date ?? date("Y-m-d");
    }

    function DB_Post(){
        $comment = $this->cnx->prepare("INSERT INTO comentario (text_comment, pos_comment, id_new, id_user, fecha_comment) VALUES (:text, :pos, :id_new, :id_user, :fecha)");
        $comment->bindParam(':text', $this->comentario, PDO::PARAM_STR);
        $comment->bindParam(':pos', $this->posicion, PDO::PARAM_INT);
        $comment->bindParam(':id_new', $this->id_new, PDO::PARAM_INT);
        $comment->bindParam(':id_user', $this->id_user, PDO::PARAM_INT);
        $comment->bindParam(':fecha', $this->fecha_comment, PDO::PARAM_STR);
        $comment->execute();
    }

    private function Mostrar(){
        $user = $this->cnx->prepare("SELECT * FROM usuario WHERE id_user = :id");
        $user->bindParam(':id', $this->id_user, PDO::PARAM_INT);
        $user->execute();
        $user = $user->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="comentario <?= $this->posicion != 0 ? "hijo" : "" ?>" id="comentario-<?= $this->id_comment ?>">
            <div class="contenido">
                <div class="autores">
                    <h2><?= "$user[nombre] $user[apellido]" ?></h2> <h6><?= (new DateTime($this->fecha_comment))->format("d-m-Y") ?></h6>
                </div>
                <p>
                    <?= $this->comentario ?>
                </p>
            </div>
            <div class="opciones">
                <span class="opcion-responder-comentario" id="responder-a-<?= $this->posicion == 0 ? $this->id_comment : $this->posicion ?>" data-value="<?= "$user[nombre] $user[apellido]" ?>">Responder</span>
            </div>
        </div>
        <?php
    }

    function MostrarComentario(){
        $hijos = $this->cnx->prepare("SELECT * FROM comentario WHERE pos_comment = :pos ORDER BY id_comment ASC");
        $hijos->bindParam(':pos', $this->id_comment, PDO::PARAM_INT);
        $hijos->execute();
        $hijos = $hijos->fetchAll(PDO::FETCH_ASSOC);

        $this->Mostrar();

        foreach ($hijos as $h){
            (new Comentario($h['id_new'], $h['id_user'], $h['text_comment'], $this->cnx, $h['pos_comment'],$h['fecha_comment'], $h['id_comment']))->Mostrar();
        }
    }
}
?>