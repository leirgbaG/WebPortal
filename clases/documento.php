<?php
class Documento{
    private $id_doc;
    private $ttle_doc;
    private $corp_doc;
    private $docx;
    private $id_clasf;
    private $id_user;
    private $cnx;

    function __construct($ttle_doc, $corp_doc, $docx, $id_clasf, $cnx, $id_user, $id_doc = null){
        $this->id_doc   = $id_doc;
        $this->id_user  = $id_user;
        $this->ttle_doc = $ttle_doc;
        $this->corp_doc = $corp_doc;
        $this->docx     = $docx;
        $this->id_clasf = $id_clasf;
        $this->cnx      = $cnx;
    }

    // Métodos 
    static function Gestion($usuario, $id, $cnx){
        date_default_timezone_set("America/Caracas");
        $fecha = date("Y-m-d");
        $gestion = $cnx->prepare("INSERT INTO usuario_documento (fecha, id_user, id_doc) VALUES (:fecha, :user, :doc)");
        $gestion->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $gestion->bindParam(':user', $usuario, PDO::PARAM_INT);
        $gestion->bindParam(':doc', $id, PDO::PARAM_INT);
        $gestion->execute();
    }
    
    function DB_Post(){
        $publicar = $this->cnx->prepare("INSERT INTO documento(ttle_doc, corp_doc, docx, id_clasf) VALUES (:ttle_doc, :corp_doc, :docx, :id_clasf)");
        $publicar->bindParam(':ttle_doc', $this->ttle_doc, PDO::PARAM_STR);
        $publicar->bindParam(':corp_doc', $this->corp_doc, PDO::PARAM_STR);
        $publicar->bindParam(':docx',     $this->docx,     PDO::PARAM_STR);
        $publicar->bindParam(':id_clasf', $this->id_clasf, PDO::PARAM_INT);
        $publicar->execute();
        
        $this->id_doc = $this->cnx->lastInsertId();
        Documento::Gestion($this->id_user, $this->id_doc, $this->cnx);
        return $this->id_doc;
    }
    

    #   **
    #   ** Mostrar Documento
    #   **
    function Mostrar_Documento($completo = true){
        // Obtener Clasificación 
        $query = $this->cnx->prepare("SELECT name_clasf FROM clasificacion WHERE id_clasf=:id_clasf");
        $query->bindParam(':id_clasf', $this->id_clasf, PDO::PARAM_INT);
        $query->execute();
        $clasf = $query->fetchColumn();


        // Obtener Autores 
        $autor = array();
        $query = $this->cnx->prepare("SELECT a.name, a.surname FROM autor a JOIN autor_documento ad ON a.id_autor = ad.id_autor WHERE id_doc=:id_doc");
        $query->bindParam(':id_doc', $this->id_doc, PDO::PARAM_INT);
        $query->execute();
        $i = 0;
        while($registroAutores = $query->fetch(PDO::FETCH_ASSOC)){
            $autor[$i] = $registroAutores['name'] . " " . $registroAutores['surname'];
            $i++;
        }
        if(!empty($autor)){
            $autores = implode(", ", $autor) . ".";
        }else{
            $autores = "Desconocido" . ".";
        }


        // Obtener Palabras Clave 
        $query = $this->cnx->prepare("SELECT pc.name_kw FROM palabraclave pc JOIN documento_palabraclave dpc ON pc.id_kw = dpc.id_kw WHERE dpc.id_doc=:id_doc");
        $query->bindParam(':id_doc', $this->id_doc, PDO::PARAM_INT);
        $query->execute();
        $keyword = $query->fetchAll(PDO::FETCH_COLUMN, 0);
        if(count($keyword) == 0){
            $keyword[0] = "No asignadas.";
        }
        
        if($completo){
            ?>
            <div class="libro" id="<?= $this->id_doc ?>" title="Presione para ver la información completa: <?= "\n".$this->ttle_doc ?>">
                <h3><?= $this->ttle_doc ?></h3> <!-- Título -->
                <div class="extraLibro">
                    <h4>De: <?= $autores ?></h4>    <!-- Autores -->
                    <h5><?= $clasf ?></h5>          <!-- Clasificación -->
                </div>
                <p class="cuerpo"><?= $this->corp_doc ?></p>    <!-- Cuerpo -->
                <h6>                            <!-- Palabras Clave -->
                    <?php
                    foreach($keyword as $kw){
                        ?>
                        <span class="kw"><?= $kw ?></span>
                        <?php
                    }
                    ?>
                </h6>
            </div>
            <?php 
        }
        ?>
        
        <div class="back_libro back_libro_oculto" id="outModal<?= $this->id_doc ?>"></div>

        <div class="modal informacion_libro" id="modal<?= $this->id_doc ?>">
            <h1><?= $this->ttle_doc ?></h1> <!-- Título -->
            <h2>De: <?= $autores ?></h2>    <!-- Autores -->
            <h3><?= $clasf ?></h3>          <!-- Clasificación -->
            <p><?= $this->corp_doc ?></p>   <!-- Cuerpo -->

            <?php if($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 1){ ?> <!-- Opciones -->
            <div>
                <a href="libro-<?= $this->id_doc ?>" title="Editar Datos">
                    <img src="images/lapiz.svg" class="editar foto-opc">
                </a>
                <div class="botonEliminarLibro" id="eliminarLibro-<?= $this->id_doc ?>" title="Eliminar Documento">
                    <img src="images/basura.svg" class="borrar foto-opc">
                </div>
            </div>
            <?php } ?>
            
            <section>                       <!-- Palabras Clave -->
            <?php
            foreach($keyword as $kw){
                echo "<h4>". $kw ."</h4>";
            }
            ?>
            </section>

            <a href="<?= $this->docx ?>"target="blank"> <!-- Descarga -->
                <p>Descargar</p>
                <img src="images/archivo-pdf.svg" class="foto_boton">
            </a>
        </div>
        <?php
    }

    # **
    # ** Hacer Resumen de Documentos.
    # **
    # **        # Esta función está diseñada para ser utilizada dentro de
                # una DataTable, por ende: Muestra la estructura de una
                # tabla, sin abrir ni cerrar su etiqueta principal «<table>».
    static function Resumen_Documentos($cnx){
        ?>
        <thead>
            <tr>
                <th>N#                  </th>
                <th>Título              </th>
                <th>Clasificación       </th>
                <th>Descripción         </th>
                <th title="Palabras Clave">P. Clave</th>
                <th>Autor               </th>
                <th>Últ. Modificación   </th>
                <th title="Usuario(s) Modificador(es)">Modificador</th>
                <th>Descargar           </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $prepare = $cnx->prepare("SELECT * FROM documento d JOIN clasificacion c ON d.id_clasf = c.id_clasf WHERE estado_doc != -1 ORDER BY d.ttle_doc DESC");
            $prepare->execute();

            
            $a = 0;
            while($row = $prepare->fetch(PDO::FETCH_ASSOC)){
                // Detectar Palabras Clave 
                $kws = $cnx->prepare("SELECT pc.name_kw FROM palabraclave pc JOIN documento_palabraclave dpc ON pc.id_kw = dpc.id_kw WHERE dpc.id_doc = :id_doc");
                $kws->bindParam(':id_doc', $row['id_doc'], PDO::PARAM_INT);
                $kws->execute();
                $kw = implode(", ", $kws->fetchAll(PDO::FETCH_COLUMN, 0)) . ".";
                
                // Detectar Autores
                $autores = $cnx->prepare("SELECT * FROM autor a JOIN autor_documento ad ON a.id_autor = ad.id_autor JOIN documento d ON ad.id_doc = d.id_doc WHERE d.id_doc = :id_doc");
                $autores->bindParam(':id_doc', $row['id_doc'], PDO::PARAM_INT);
                $autores->execute();
                $autor = array();
                $n = 0;
                while($aut = $autores->fetch(PDO::FETCH_ASSOC)){
                    $autor[$n] = $aut['name'] . " " . $aut['surname'];
                    $n++;
                }
                $autor = implode(", ", $autor) . ".";

                // Información de modificación 
                $mods = $cnx->prepare("SELECT ud.fecha, u.usuario FROM usuario_documento ud 
                                                                        JOIN usuario u      ON u.id_user = ud.id_user 
                                                                        JOIN documento d    ON d.id_doc = ud.id_doc 
                                            WHERE d.id_doc = :id_doc ORDER BY ud.id_userdoc DESC");
                $mods->bindParam(':id_doc', $row['id_doc'], PDO::PARAM_INT);
                $mods->execute();
                $mod = array();
                $fecha = "Desconocido";
                $n = 0;
                $usuarios = [];

                while($m = $mods->fetch(PDO::FETCH_ASSOC)){
                    if($n == 0){
                        $fecha = $m['fecha'];
                        $usuarios[$n] = $m['usuario'];
                        $mod[$n] = $m['usuario'];
                    }else{
                        $comprobacion = true;
                        foreach($usuarios as $u){
                            if($u == $m['usuario'])
                                $comprobacion = false;
                        }

                        if($comprobacion){
                            $mod[$n] = $m['usuario'];
                        }
                    }
                    $n++;
                }
                $usuario = implode(", ", $mod);
                $usuario = $usuario == "" ? "Desconocido." : "@$usuario";

                $a++;

                ?>
                <tr>
                    <td><?= $a                ?></td><!-- Número de Fila    -->
                    <td><?= $row['ttle_doc']  ?></td><!-- Título            -->
                    <td><?= $row['name_clasf']?></td><!-- Clasificación     -->
                    <td>                             <!-- Descripción       -->
                        <div class="seeMore" id="<?= $row['id_doc'] ?>">Ver más...</div>
                        <?php
                        $doc = new Documento($row['ttle_doc'], $row['corp_doc'], $row['docx'], $row['id_clasf'], $cnx, null, $row['id_doc']);
                        $doc->Mostrar_Documento(false);
                        ?>
                    </td>
                    <td><?= $kw               ?></td><!-- Palabras Clave    -->
                    <td><?= $autor            ?></td><!-- Autores           -->
                    <td><?= $fecha            ?></td><!-- Últ. Modificación -->
                    <td><?= $usuario          ?></td><!-- Modificador       -->
                    <td>                             <!-- Descargar         -->
                        <a target="_blank" href="<?= $row['docx'] ?>">
                            <img src="images/archivo-pdf.svg" class="dt_row_opc descarga">
                        </a>
                    </td>
                </tr>
                <?php } ?>
        </tbody>
        <?php
    }
}