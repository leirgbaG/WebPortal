<?php
    class Usuario{
        private $id;
        private $prenom;
        private $nom;
        private $user;
        private $pass;
        private $tipo;
        private $cargo;
        private $estado;
        private $cnx;

        //  Getter
        public function GetCargo(){
            $prepare = $this->cnx->prepare("SELECT name_cargo FROM cargo WHERE id_cargo=:cargo");
            $prepare->bindParam(':cargo', $this->cargo, PDO::PARAM_INT);
            $prepare->execute();
            $resultado = $prepare->fetchColumn();
            return $resultado ?? 'No hay cargo asignado';
        }

        // Métodos 
        function __construct($prenom, $nom, $user, $pass, $cargo, $cnx, $tipo = null, $id = null, $estado = null){
            $this->prenom   = $prenom;
            $this->nom      = $nom;
            $this->user     = $user;
            $this->pass     = password_hash($pass, PASSWORD_DEFAULT);
            $this->cargo    = $cargo;
            $this->cnx      = $cnx;
            $this->tipo     = $tipo ?? 0;
            $this->id       = $id;
            $this->estado   = $estado ?? 0;
        }

        function DB_Post(){
            $prepare = $this->cnx->prepare("INSERT INTO usuario(nombre, apellido, usuario, contrasegna, tipo, id_cargo) VALUES (:prenom, :nom, :user, :pass, :tipo, :cargo)");
            $prepare->bindParam(':prenom', $this->prenom, PDO::PARAM_STR);
            $prepare->bindParam(':nom', $this->nom, PDO::PARAM_STR);
            $prepare->bindParam(':user', $this->user, PDO::PARAM_STR);
            $prepare->bindParam(':pass', $this->pass, PDO::PARAM_STR);
            $prepare->bindParam(':tipo', $this->tipo, PDO::PARAM_INT);
            $prepare->bindParam(':cargo', $this->cargo, PDO::PARAM_INT);
            $prepare->execute();

            $this->id = $this->cnx->lastInsertId();
            return $this->id;
        }

        function MostrarUsuario($block = true){
            $tipo   = ($this->tipo == 2) ? "Moderador(a)" : (($this->tipo) ? "Administrador(a)" : "Visitante");
            $cargo  = $this->GetCargo();

            $sentencia = $this->cnx->prepare("SELECT COUNT(*) FROM usuario JOIN noticia ON noticia.id_user = usuario.id_user WHERE usuario=:user");
            $sentencia->bindParam(':user', $this->user, PDO::PARAM_STR);
            $sentencia->execute();
            $noticias = $sentencia->fetchColumn();

            $sentencia = $this->cnx->prepare("SELECT COUNT(*) FROM usuario_documento WHERE id_user=:id_usu");
            $sentencia->bindParam(':id_usu', $this->id, PDO::PARAM_INT);
            $sentencia->execute();
            $documentos = $sentencia->fetchColumn();

            $textBlock = $this->estado == 1 ? "Desbloquear Usuario" : "Bloquear Usuario";
            $tipoBlock = $this->estado == 1 ? 0 : 1;
            ?>
            <div class='informacionUsuario' id="<?= $this->id ?>">
                <h2><?= $this->prenom . " " . $this->nom . '-' . $tipo ?></h2>
                <h6>@<?= $this->user ?></h6>
                <h5><?= $cargo ?></h5>
                <p>
                    Noticias publicadas: <?= $noticias ?>.<br>
                    Documentos gestionados: <?= $documentos ?>.
                </p>
                <div class='opciones'>
                    <div class="eliminar boton" id="eliminarUsuario-<?= $this->id ?>">Eliminar Cuenta</div>
                    
                    <?php if($block && $this->user != $_SESSION['usuario']) { ?>
                        <div title="Esta función prohíbe a esta cuenta realizar acciones o ingresar en el sistema." class="boton bloquear" id="bloquearUsuario-<?= $this->id ?>-<?= $tipoBlock ?>"> <?= $textBlock ?></div>
                    <?php } ?>
                    
                    <div onclick="window.location.href='usuario-<?= $this->user ?>'" class="boton">Modificar Datos</div>
                </div>
            </div>
            <?php
        }

        static function Resumen_Usuarios($cnx){
            ?>
            <thead>
                <tr>
                    <th>#N</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cargo</th>
                    <th>Tipo</th>
                    <th title="Noticias Publicadas">Noticias</th>
                    <th title="Documentos Gestionados">Docs</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $prepare = $cnx->prepare("SELECT * FROM usuario JOIN cargo ON usuario.id_cargo = cargo.id_cargo WHERE estado_user != -1");
            $prepare->execute();
            $a=0;
            while($row = $prepare->fetch(PDO::FETCH_ASSOC)){
                // Recibiendo Noticias 
                $news = $cnx->prepare("SELECT COUNT(*) FROM noticia WHERE id_user = :id");
                $news->bindParam(":id", $row["id_user"], PDO::PARAM_INT);
                $news->execute();
                // Recibiendo Documentos 
                $docs = $cnx->prepare("SELECT COUNT(*) FROM usuario_documento WHERE id_user = :id");
                $docs->bindParam(":id", $row["id_user"], PDO::PARAM_INT);
                $docs->execute();
                
                // Comprobando Estado
                $textBlock = $row['estado_user'] == 1 ? "images/descubrir.svg" : "images/prohibicion.svg";
                $tipoBlock = $row['estado_user'] == 1 ? 0 : 1;
                $ttleBlock = $row['estado_user'] == 1 ? "Desbloquear Usuario" : "Bloquear Usuario";

                // Revalidando Datos
                $a++;
                $tipo = ($row['tipo'] == 1) ? "Administrador" : (($row['tipo'] == 2) ? "Moderador" : "Visitante");

                // Mostrando
                ?>
                <tr>
                    <td><?= $a                   ?></td><!-- N#         -->
                    <td><?= "@$row[usuario]"     ?></td><!-- Usuario    -->
                    <td><?= $row['nombre']       ?></td><!-- Nombre     -->
                    <td><?= $row['apellido']     ?></td><!-- Apellido   -->
                    <td><?= $row['name_cargo']   ?></td><!-- Cargo      -->
                    <td><?= $tipo                ?></td><!-- Tipo       -->
                    <td><?= $news->fetchColumn() ?></td><!-- Noticias   -->
                    <td><?= $docs->fetchColumn() ?></td><!-- Documentos -->
                    <td>                                <!-- Opciones   -->
                        <!-- Modificar -->
                        <a href="usuario-<?=$row['usuario']?>" title="Modificar Usuario">
                            <img src="images/lapiz.svg" class="dt_row_opc editar">
                        </a>
                        
                        <!-- Eliminar -->
                        <span class="eliminar" id="eliminarUsuario-<?= $row['id_user'] ?>" title="Eliminar Usuario">
                            <img src="images/basura.svg" class="dt_row_opc eliminar">
                        </span>

                        <!-- Bloquear -->
                        <span class="bloquear" id="bloquearUsuario-<?= $row['id_user'] ?>-<?= $tipoBlock ?>" title="<?= $ttleBlock ?>">
                            <img src='<?= $textBlock ?>' class="dt_row_opc eliminar">
                        </span>
                    </td>
                </tr>
                <?php

            }
        }
    }

    class Cargo{
        private $id;
        private $nombre;
        private $cnx;

        function __construct($name, $cnx){
            $this->nombre = $name;
            $this->cnx = $cnx;
        }

        function DB_Post(){
            $prepare = $this->cnx->prepare("INSERT INTO cargo (name_cargo) VALUES (:nombre)");
            $prepare->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $prepare->execute();
        }

        function GetID($name = 0){
            $prepare = $this->cnx->prepare("SELECT id_cargo FROM cargo WHERE name_cargo=:nombre");
            ($name == 0)? $prepare->bindParam(':nombre', $this->nombre, PDO::PARAM_STR)
                        : $prepare->bindParam(':nombre', $name, PDO::PARAM_STR);
            return $prepare->execute();
        }
    }
?>