<?php
// Sesión 
session_start();
if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if($_SESSION['tipo'] != 2){
    header("Location: ../cuenta");
    exit;
}

// Base de Datos
include_once("../clases/conexion.php");
date_default_timezone_set("America/Caracas");

//  Librería FPDF
require_once('../FPDF/fpdf.php');

class PDF extends FPDF{
    function Header(){
        $this->Image("../images/logoSACN.png", 30, 0, 28);
        
        $this->SetFont('Arial', 'B', 18);
        $this->SetXY(60, 7);
        $this->MultiCell(96, 8, "Seguridad Alimentaria\ny Cultura Nutricional", 0, 'C');
        
        $this->Image("../images/logoUPTP.png", 158, 6, 28);

        
        $this->SetXY(30, 30);
    }
}


/*
**
**  Creación de las hojas PDF
**
*/
$PDF = new PDF('P', 'mm', 'Letter');
$PDF->SetMargins(30, 30, 30);
$PDF->AddPage();
$PDF->SetFont('Arial', '', 11);
$PDF->SetFillColor(217, 255, 225);
$PDF->Ln();


/*
**
**  Recolección de los datos para la búsqueda
**  dentro de la base de datos, según el formulario
**  previamente rellenado.
**
*/

#   Definir Tipo de Reporte

switch($_POST['tipo_reporte']){
    case "Noticia":
        /*
        **
        **  CASE: Noticia
        **      Se desea conocer las Noticias publicadas
        **      en el sistema con su multimedia.
        **
        */

        # Seleccionar Tiempo
        switch($_POST['periodo_tiempo']){
            case 'hoy': 
                $fecha_inicio = new DateTime();
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'semana': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-7 days');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'mes': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'trimestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-3 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'semestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-6 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'anno': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 year');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'personalizado':
                $fecha_inicio = (new DateTime($_POST['inicio_periodo']))->format('Y-m-d');
                $fecha_final = (new DateTime($_POST['final_periodo']))->format('Y-m-d');
                break;
        }
        $fecha_final = isset($fecha_final) ? $fecha_final : (new DateTime())->format('Y-m-d');
        
        $betweenFecha_SQL = $_POST['periodo_tiempo'] != 'todos' ? "(n.pet_fecha BETWEEN '$fecha_inicio' AND '$fecha_final')" : '';

        #Seleccionar Usuario
        if(!isset($_POST['id_user']) || $_POST['id_user'][0] == 0 ){
            if(count($_POST['tipo_usuario']) == 2){
                $whereUsuario_SQL = "(tipo = ". $_POST['tipo_usuario'][0] .") OR (tipo = ". $_POST['tipo_usuario'][1] .") ";
            }elseif(count($_POST['tipo_usuario']) == 1){
                $whereUsuario_SQL = "(tipo = ". $_POST[0]['tipo_usuario'] .") ";
            }else{
                $whereUsuario_SQL = '';
            }
        }else{
            $usuarios = [];
            foreach($_POST['id_user'] as $id_user){
                $usuarios[] = "u.id_user = ". $id_user;
            }
            $whereUsuario_SQL = '('. implode(' OR ', $usuarios) .') ';
        }

        # Seleccionar Noticias
        $SQL = "SELECT * FROM noticia n JOIN usuario u ON n.id_user = u.id_user ";
        if($_POST['periodo_tiempo'] != 'todos') $SQL .= "WHERE $betweenFecha_SQL ";
        if($whereUsuario_SQL && $betweenFecha_SQL) $SQL .= "AND $whereUsuario_SQL ";
        elseif($whereUsuario_SQL) $SQL .= "WHERE $whereUsuario_SQL ";

        $noticias = $cnx->prepare($SQL . "ORDER BY n.id_new DESC");
        $noticias->execute();
        $noticias = $noticias->fetchAll(PDO::FETCH_ASSOC);


        # Reemplazar Datos
        foreach($noticias as $i => $n){
            $noticias[$i]['corp_new'] = str_replace("<br>", "\n", $n['corp_new']);

            $noticias[$i]['fecha_new'] = (new DateTime($n['fecha_new']))->format("d/m/Y");
            $noticias[$i]['hora_new'] = (new DateTime($n['hora_new']))->format("H:i");

            $noticias[$i]['estado_new'] = $n['estado_new'] == -1 ? "Eliminada" : ($n['estado_new'] == 1 ? "Publicada" : ($n['estado_new'] == 2 ? "Oculta" : ($n['estado_new'] == 3 ? "Esperando eliminación" : "Esperando aprobación")));

            $noticias[$i]['media'] = $cnx->prepare("SELECT * FROM multimedia WHERE id_new = $n[id_new]");
            $noticias[$i]['media']->execute();
            $noticias[$i]['media'] = $noticias[$i]['media']->fetchAll(PDO::FETCH_ASSOC);
        }

        # Mostrar Título
        $PDF->SetFont('Arial', 'B', 12);
        $a = count($noticias);
        $PDF->Cell(0, 8, "Reporte de Noticias ($a)", 0, 1, 'C');
        $PDF->SetFont('Arial', '', 11);

        
        # Mostrar Noticia
        foreach($noticias as $n){
            $PDF->MultiCell(0, 6, mb_convert_encoding($n['ttle_new'], 'ISO-8859-1', 'auto'), "LTR", 'C', true);
            $PDF->MultiCell(0, 6, mb_convert_encoding($n['corp_new'], 'ISO-8859-1', 'auto'), 1, 'J');
            
            foreach($n['media'] as $m){
                if(!preg_match("/mp4$/", $m['media'])){
                    $PDF->Image("../$m[media]", 30, null, null, 50);
                    $PDF->Ln(1);
                }
            }
            
            $PDF->SetFont('Arial', 'I', 11);
            $PDF->Cell(78, 8, mb_convert_encoding("Publicado por: @". $n['usuario'] ." el $n[fecha_new] a las $n[hora_new].", 'ISO-8859-1', 'auto'));
            $PDF->SetFont('Arial', 'BI', 11);
            $PDF->Cell(78, 8, mb_convert_encoding($n['estado_new'], 'ISO-8859-1', 'auto'), 0, 1, 'R');
            $PDF->SetFont('Arial', '', 11);
            

            $PDF->Ln(8);
        }

        break;

    case "Documento":
        /*
        **
        **  CASE: DOCUMENTO
        **      Se desean conocer los Documentos publicados
        **      en el sistema. Se desea saber su título, nombre, palabras clave, 
        **      descripción.
        **
        */

        # Seleccionar Tiempo
        switch($_POST['periodo_tiempo']){
            case 'hoy': 
                $fecha_inicio = new DateTime();
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'semana': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-7 days');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'mes': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'trimestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-3 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'semestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-6 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'anno': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 year');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'personalizado':
                $fecha_inicio = (new DateTime($_POST['inicio_periodo']))->format('Y-m-d');
                $fecha_final = (new DateTime($_POST['final_periodo']))->format('Y-m-d');
                break;
        }
        $fecha_final = isset($fecha_final) ? $fecha_final : (new DateTime())->format('Y-m-d');
        
        $betweenFecha_SQL = $_POST['periodo_tiempo'] != 'todos' ? "(ud.fecha BETWEEN '$fecha_inicio' AND '$fecha_final')" : '';

        #Seleccionar Usuario
        if(!isset($_POST['id_user']) || $_POST['id_user'][0] == 0 ){
            if(count($_POST['tipo_usuario']) == 2){
                $whereUsuario_SQL = "(u.tipo = ". $_POST['tipo_usuario'][0] ." OR u.tipo = ". $_POST['tipo_usuario'][1] .") ";
            }elseif(count($_POST['tipo_usuario']) == 1){
                $whereUsuario_SQL = "(u.tipo = ". $_POST[0]['tipo_usuario'] .") ";
            }else{
                $whereUsuario_SQL = '';
            }
        }else{
            $usuarios = [];
            foreach($_POST['id_user'] as $id_user){
                $usuarios[] = "u.id_user = ". $id_user;
            }
            $whereUsuario_SQL = '('. implode(' OR ', $usuarios) .') ';
        }



        # Seleccionar Solicitudes
        $SQL = "SELECT * FROM usuario_documento ud JOIN usuario u ON ud.id_user = u.id_user JOIN documento d ON ud.id_doc = d.id_doc JOIN clasificacion c ON c.id_clasf = d.id_clasf ";
        if($_POST['periodo_tiempo'] != 'todos') $SQL .= "WHERE $betweenFecha_SQL ";
        if($whereUsuario_SQL && $betweenFecha_SQL) $SQL .= "AND $whereUsuario_SQL ";
        elseif($whereUsuario_SQL) $SQL .= "WHERE $whereUsuario_SQL ";

        $documentos = $cnx->prepare($SQL . "ORDER BY d.ttle_doc ASC");
        $documentos->execute();
        $documentos = $documentos->fetchAll(PDO::FETCH_ASSOC);

        #   Modificar Datos
        $count = 0;
        foreach($documentos as $i => $d){
            $documentos[$i]['corp_doc'] = str_replace("<br>", "\n", $d['corp_doc']);

            $documentos[$i]['estado_doc'] = $d['estado_doc'] ? true : false;

            if($i == 0) $count++;
            if($i > 0 && $d['id_doc'] != $documentos[$i-1]['id_doc']) $count++;
        }

        #   Mostrar Título

        $PDF->SetFont('Arial', 'B', 12);
        $PDF->Cell(0, 10, "Reporte de Documentos ($count)", 0, 1, 'C');
        $PDF->Ln(8);

        #   Mostrar Datos
        foreach($documentos as $i => $d){
            if($i > 0 && $d['id_doc'] == $documentos[$i-1]['id_doc']) continue;

            $PDF->SetFont('', 'B', 11);
            $PDF->MultiCell(0, 6, mb_convert_encoding($d['ttle_doc'], 'ISO-8859-1'), 'LTR', 'C', true);
            $PDF->SetFont('');
            $PDF->MultiCell(0, 6, mb_convert_encoding($d['corp_doc'], 'ISO-8859-1'), 'LTR', 'J');
            
            $PDF->SetFont('', 'B');
            $PDF->Cell(30, 8, mb_convert_encoding("Clasificación:", 'ISO-8859-1'), "LTR", 0, 'R', true);
            $PDF->SetFont('');
            $PDF->Cell(0, 8, mb_convert_encoding($d['name_clasf'], 'ISO-8859-1'), "LTR", 1, 'J');
            
            $PDF->SetFont('', 'B');
            $PDF->Cell(0, 8, mb_convert_encoding("Palabras Clave:", 'ISO-8859-1'), "LTR", 1, 'J', true);
            $PDF->SetFont('');

            $dkws = $cnx->prepare("SELECT k.* FROM documento_palabraclave dk JOIN palabraclave k ON dk.id_kw = k.id_kw WHERE dk.id_doc = :id_doc");
            $dkws->bindParam(':id_doc', $d['id_doc'], PDO::PARAM_INT);
            $dkws->execute();
            $dkws = $dkws->fetchAll(PDO::FETCH_ASSOC);

            $PDF->MultiCell(0, 8, mb_convert_encoding(implode(', ', array_column($dkws, 'name_kw')) . ".", "ISO-8859-1"), 1, 'L');
            
            
            $PDF->SetFont('Arial', 'I', 10);
            $PDF->Cell(100, 8, mb_convert_encoding("Últ. Modificación: ". (new DateTime($d['fecha']))->format('d/m/Y'). " por: $d[nombre] $d[apellido]", "ISO-8859-1"), 0, !$d['estado_doc'], 'J');
            
            if($d['estado_doc']){
                $PDF->SetFont('Arial', 'IB', 11);
                $PDF->Cell(56, 8, "Eliminado", 0, 1, 'R', false, '../'.$d['docx']);
                $PDF->SetFont('Arial', '', 11);
            }

            $PDF->Ln();
        }
        break;

    case "Solicitud":
        /*
        **
        **  CASE: SOLICITUD
        **      Se desea conocer las solicitudes de documentos
        **      realizadas en el sistema.
        **
        */

        # Seleccionar Tiempo
        switch($_POST['periodo_tiempo']){
            case 'hoy': 
                $fecha_inicio = new DateTime();
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'semana': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-7 days');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'mes': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'trimestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-3 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'semestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-6 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'anno': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 year');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'personalizado':
                $fecha_inicio = (new DateTime($_POST['inicio_periodo']))->format('Y-m-d');
                $fecha_final = (new DateTime($_POST['final_periodo']))->format('Y-m-d');
                break;
        }
        $fecha_final = isset($fecha_final) ? $fecha_final : (new DateTime())->format('Y-m-d');
        
        $betweenFecha_SQL = $_POST['periodo_tiempo'] != 'todos' ? "(p.pet_fecha BETWEEN '$fecha_inicio' AND '$fecha_final')" : '';

        #Seleccionar Usuario
        if(!isset($_POST['id_user']) || $_POST['id_user'][0] == 0 ){
            if(count($_POST['tipo_usuario']) == 2){
                $whereUsuario_SQL = "(tipo = ". $_POST['tipo_usuario'][0] .") OR (tipo = ". $_POST['tipo_usuario'][1] .") ";
            }elseif(count($_POST['tipo_usuario']) == 1){
                $whereUsuario_SQL = "(tipo = ". $_POST[0]['tipo_usuario'] .") ";
            }else{
                $whereUsuario_SQL = '';
            }
        }else{
            $usuarios = [];
            foreach($_POST['id_user'] as $id_user){
                $usuarios[] = "u.id_user = ". $id_user;
            }
            $whereUsuario_SQL = '('. implode(' OR ', $usuarios) .') ';
        }

        # Seleccionar Solicitudes
        $SQL = "SELECT * FROM peticion p JOIN usuario u ON p.id_user = u.id_user ";
        if($_POST['periodo_tiempo'] != 'todos') $SQL .= "WHERE $betweenFecha_SQL ";
        if($whereUsuario_SQL && $betweenFecha_SQL) $SQL .= "AND $whereUsuario_SQL ";
        elseif($whereUsuario_SQL) $SQL .= "WHERE $whereUsuario_SQL ";

        $peticiones = $cnx->prepare($SQL . "ORDER BY pet_estado ASC");
        $peticiones->execute();
        $peticiones = $peticiones->fetchAll(PDO::FETCH_ASSOC);


        # Modificar Datos
        foreach($peticiones as $i => $p){
            $peticiones[$i]['pet_estado'] = $p['pet_estado'] == 2 ? "Rechazada" : ($p['pet_estado'] == 1 ? "Aprobada" : "Sin Respuesta");

            $peticiones[$i]['pet_corp'] = str_replace("<br>", "\n", $p['pet_corp']);
            $peticiones[$i]['pet_msg'] = str_replace("<br>", "\n", $p['pet_msg']);

            $peticiones[$i]['pet_fecha'] = (new DateTime($p['pet_fecha']))->format("d/m/Y");
        }

        # Mostrar Título
        $PDF->SetFont('Arial', 'B', 12);
        $a = count($peticiones);
        $PDF->Cell(0, 10, "Reporte de Peticiones ($a)", 0, 1, 'C');
        $PDF->Ln(8);
        $PDF->SetFillColor(217, 255, 225);


        # Mostrar Datos
        foreach($peticiones as $i => $p){
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->MultiCell(0, 6, mb_convert_encoding($p['pet_ttle'], "ISO-8859-1"), "LTR", 'C', True);
            $PDF->SetFont('Arial', '', 11);
            $PDF->MultiCell(0, 6, mb_convert_encoding($p['pet_corp'], "ISO-8859-1"), 1, 'J', false);
            if($p['pet_msg']){
                $PDF->SetFont('Arial', 'B', 11);
                $PDF->Cell(0, 6, "Respuesta: ", 1, 1, 'L', true);
                $PDF->SetFont('Arial', '', 11);
                $PDF->MultiCell(0, 6, mb_convert_encoding($p['pet_msg'], "ISO-8859-1"), "RLB");
            }
            $PDF->SetFont('Arial', 'I', 11);
            $PDF->Cell(78, 6, mb_convert_encoding("Solicitud de @". $p['usuario'] ." el ". $p['pet_fecha'], "ISO-8859-1"), 0, 0, 'L');
            
            if($p['pet_estado'] == 'Rechazada') $PDF->SetTextColor(206, 36, 36);
            if($p['pet_estado'] == 'Aprobada') $PDF->SetTextColor(13, 180, 80);
            $PDF->SetFont('Arial', 'IB', 11);
            $PDF->Cell(78, 6, mb_convert_encoding($p['pet_estado'], "ISO-8859-1"), 0, 1, 'R');
            $PDF->SetTextColor(0);

            $PDF->Ln(8);
        }
        break;

    case "Usuario":
        /*
        **
        **  CASE: USUARIO
        **      Se desea conocer los datos de los usuarios, así como
        **      la cantidad de noticias, documentos, solicitudes y 
        **      acciones que haya realizado cada usuario.
        **
        */

        #   Obtención de usuarios
        $usuarios = [];
        $cantidades = [];
        $titulo = [];
        if(in_array(2, $_POST['tipo_usuario'])){
            $a = $cnx->prepare("SELECT * FROM usuario u JOIN cargo c ON u.id_cargo = c.id_cargo WHERE u.tipo = 2 ORDER BY nombre ASC");
            $a->execute();
            $cantidades['Moderador'] = $a->rowCount();
            while($b = $a->fetch(PDO::FETCH_ASSOC)){
                array_push($usuarios, $b);
            }
            array_push($titulo, "Moderadores");
        }
        if(in_array(1, $_POST['tipo_usuario'])){
            $a = $cnx->prepare("SELECT * FROM usuario u JOIN cargo c ON u.id_cargo = c.id_cargo WHERE u.tipo = 1 ORDER BY nombre ASC");
            $a->execute();
            $cantidades['Administrador'] = $a->rowCount();
            while($b = $a->fetch(PDO::FETCH_ASSOC)){
                array_push($usuarios, $b);
            }
            array_push($titulo, "Administradores");
        }
        if(in_array(0, $_POST['tipo_usuario'])){
            $a = $cnx->prepare("SELECT * FROM usuario u JOIN cargo c ON u.id_cargo = c.id_cargo WHERE u.tipo = 0 ORDER BY nombre ASC");
            $a->execute();
            $cantidades['Visitante'] = $a->rowCount();
            while($b = $a->fetch(PDO::FETCH_ASSOC)){
                array_push($usuarios, $b);
            }
            array_push($titulo, "Visitantes");
        }
        
        #   Obtención de otros datos
        foreach($usuarios as $a => $user){
            #CANTIDAD DE NOTICIAS
            $cant_noticias = $cnx->prepare("SELECT COUNT(*) FROM noticia WHERE id_user = :id");
            $cant_noticias->bindParam(':id', $user['id_user']);
            $cant_noticias->execute();
            $usuarios[$a]['noticias'] = $cant_noticias->fetchColumn();

            #CANTIDAD DE DOCUMENTOS
            $cant_doc = $cnx->prepare("SELECT COUNT(*) FROM usuario_documento WHERE id_user = :id");
            $cant_doc->bindParam(':id', $user['id_user']);
            $cant_doc->execute();
            $usuarios[$a]['documentos'] = $cant_doc->fetchColumn();

            #CANTIDAD DE SOLICITUDES DE DOCUMENTO
            $cant_pet = $cnx->prepare("SELECT COUNT(*) FROM peticion WHERE id_user = :id");
            $cant_pet->bindParam(':id', $user['id_user']);
            $cant_pet->execute();
            $usuarios[$a]['solicitudes'] = $cant_pet->fetchColumn();

            #CANTIDAD DE ACCIONES
            $cant_acc = $cnx->prepare("SELECT COUNT(*) FROM accion WHERE id_user = :id");
            $cant_acc->bindParam(':id', $user['id_user']);
            $cant_acc->execute();
            $usuarios[$a]['acciones'] = $cant_acc->fetchColumn();

            #ESTADO USUARIO
            switch($user['estado_user']){
                case 0: $usuarios[$a]['estado'] = 'Activo'; break;
                case 1: $usuarios[$a]['estado'] = 'Bloqueado'; break;
                case -1: $usuarios[$a]['estado'] = 'Eliminado'; break;
            }

            #TIPO USUARIO
            switch($user['tipo']){
                case 1: $usuarios[$a]['tipo'] = 'Administrador'; break;
                case 2: $usuarios[$a]['tipo'] = 'Moderador'; break;
                default: $usuarios[$a]['tipo'] = 'Visitante'; break;
            }
        }


        #   Título del Reporte
        $titulo = (count($titulo) > 2 ? "Reporte de Usuarios" : "Reporte de ". implode(", ", $titulo)) . " (" . count($usuarios) . ")";
        $PDF->SetFont('Arial', 'B', 12);
        $PDF->Cell(0, 10, $titulo, 0, 1, 'C');
        $PDF->Ln(8);


        #   Mostrar Datos
        foreach($usuarios as $i => $u){

            if($i == 0 || $usuarios[$i-1]['tipo'] != $u['tipo']){
                $PDF->SetFont('Arial', 'B', 11);
                $subttle = "Mostrando $u[tipo]";
                $subttle = preg_match("/e$/", $subttle) ? $subttle . 's ' : $subttle . "es ";
                $subttle .= "(" . $cantidades[$u['tipo']] . "):";
                $PDF->Cell(0, 8, $subttle, 0, 1);
            }
            
            $PDF->SetFont('Arial', '', 11);

            #Nombre
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(19, 8, "Nombre: ", "TLR", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(59, 8, mb_convert_encoding($u['nombre'], "ISO-8859-1"), "T", 0);

            #Apellido
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(19, 8, "Apellido: ", "TRL", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(59, 8, mb_convert_encoding($u['apellido'], "ISO-8859-1"), "TR", 1);

            #Cargo
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(19, 8, "Cargo: ", "TRL", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(59, 8, mb_convert_encoding($u['name_cargo'], "ISO-8859-1"), "T", 0);
            
            #Tipo
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(19, 8, "Tipo: ", "TLR", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(59, 8, mb_convert_encoding($u['tipo'], "ISO-8859-1"), "TR", 1);
            
            #Usuario
            $PDF->Cell(78, 8, mb_convert_encoding("@$u[usuario]", "ISO-8859-1"), "TL", 0, "", true);
            
            #Estado
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(19, 8, "Estado: ", "TRL", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(59, 8, mb_convert_encoding($u['estado'], "ISO-8859-1"), "TR", 1);

            if($u['tipo'] != "Visitante" || $u['noticias'] > 0 || $u['documentos'] > 0){
                #Cantidad Noticias
                $PDF->SetFont('Arial', 'B', 11);
                $PDF->Cell(54, 8, "# Noticias Publicadas: ", "TRL", 0, "", true);
                $PDF->SetFont('Arial', '', 11);
                $PDF->Cell(24, 8, $u['noticias'], "T", 0);
    
                #Cantidad Documentos
                $PDF->SetFont('Arial', 'B', 11);
                $PDF->Cell(54, 8, "# Documentos Gestionados: ", "TRL", 0, "", true);
                $PDF->SetFont('Arial', '', 11);
                $PDF->Cell(24, 8, $u['documentos'], "TR", 1);
            }
            
            #Cantidad de Acciones
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(54, 8, "# Acciones Realizadas: ", "TLRB", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(24, 8, $u['acciones'], "TB", 0);
            
            #Cantidad de Solicitudes
            $PDF->SetFont('Arial', 'B', 11);
            $PDF->Cell(54, 8, "# Cantidad de Solicitudes: ", "TLRB", 0, "", true);
            $PDF->SetFont('Arial', '', 11);
            $PDF->Cell(24, 8, $u['solicitudes'], "TBR", 1);
            
            $PDF->Ln();
        }
        break;

    case "Accion":
        /*
        **
        **  CASE: ACCIÓN
        **      Se desea conocer las acciones realizadas
        **      por los usuarios en el sistema.
        **
        */

        # Seleccionar Tiempo
        switch($_POST['periodo_tiempo']){
            case 'hoy': 
                $fecha_inicio = new DateTime();
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'semana': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-7 days');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'mes': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;
                
            case 'trimestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-3 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'semestre': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-6 month');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'anno': 
                $fecha_inicio = new DateTime();
                $fecha_inicio->modify('-1 year');
                $fecha_inicio = $fecha_inicio->format('Y-m-d');
                break;

            case 'personalizado':
                $fecha_inicio = (new DateTime($_POST['inicio_periodo']))->format('Y-m-d');
                $fecha_final = (new DateTime($_POST['final_periodo']))->format('Y-m-d');
                break;
        }
        $fecha_final = isset($fecha_final) ? $fecha_final : (new DateTime())->format('Y-m-d');
        
        $betweenFecha_SQL = $_POST['periodo_tiempo'] != 'todos' ? "(a.fecha_act BETWEEN '$fecha_inicio' AND '$fecha_final')" : '';


        #Seleccionar Usuario
        if(!isset($_POST['id_user']) || $_POST['id_user'][0] == 0 ){
            if(count($_POST['tipo_usuario']) == 2){
                $whereUsuario_SQL = "(tipo = ". $_POST['tipo_usuario'][0] .") OR (tipo = ". $_POST['tipo_usuario'][1] .") ";
            }elseif(count($_POST['tipo_usuario']) == 1){
                $whereUsuario_SQL = "(tipo = ". $_POST[0]['tipo_usuario'] .") ";
            }else{
                $whereUsuario_SQL = '';
            }
        }else{
            $usuarios = [];
            foreach($_POST['id_user'] as $id_user){
                $usuarios[] = "u.id_user = ". $id_user;
            }
            $whereUsuario_SQL = '('. implode(' OR ', $usuarios) .') ';
        }

        # Seleccionar Acciones
        $SQL = "SELECT * FROM accion a JOIN usuario u ON a.id_user = u.id_user ";
        if($_POST['periodo_tiempo'] != 'todos') $SQL .= "WHERE $betweenFecha_SQL ";
        if($whereUsuario_SQL && $betweenFecha_SQL) $SQL .= "AND $whereUsuario_SQL ";
        elseif($whereUsuario_SQL) $SQL .= "WHERE $whereUsuario_SQL ";

        $acciones = $cnx->prepare($SQL . "ORDER BY id_accion DESC");
        $acciones->execute();
        $acciones = $acciones->fetchAll(PDO::FETCH_ASSOC);


        # Mostrar Título
        $PDF->SetFont('Arial', 'B', 12);
        $a = count($acciones);
        $PDF->Cell(0, 10, "Reporte de Acciones ($a)", 0, 1, 'C');
        $PDF->SetFont('Arial', '', 11);
        $PDF->Ln(8);

        # Título de la Tabla
        $PDF->Cell(10, 8, "#N", "LT", 0, 'C', true);
        $PDF->Cell(68, 8, mb_convert_encoding("Nombre de la Acción", "ISO-8859-1"), "LT", 0, 'C', true);
        $PDF->Cell(40, 8, "Usuario", "LT", 0, 'C', true);
        $PDF->Cell(23, 8, "Fecha", "LT", 0, 'C', true);
        $PDF->Cell(15, 8, "Hora", "LTR", 1, 'C', true);
        
        $PDF->SetFillColor(240, 255, 225);

        # Mostrar Datos
        foreach($acciones as $i => $a){
            $PDF->Cell(10, 8, $i+1, 1, 0, 'C', $i%2 == 0);
            $PDF->Cell(68, 8, mb_convert_encoding($a['accion_name'], "ISO-8859-1"), 1, 0, 'C', $i%2 == 0);
            $PDF->Cell(40, 8, $a['usuario'], 1, 0, 'C', $i%2 == 0);
            $PDF->Cell(23, 8, (new DateTime($a['fecha_act']))->format("d/m/Y"), 1, 0, 'C', $i%2 == 0);
            $PDF->Cell(15, 8, (new DateTime($a['hora_act']))->format("H:i"), 1, 1, 'C', $i%2 == 0);
        }
        if(count($acciones) == 0) $PDF->Cell(0, 8, "No hay datos para mostrar", 1, 1, 'C', true);

        break;

    default:
        header(("Location: ../reporte-e"));
        exit;
        break;
}

$PDF->Output('', 'repote.pdf', true);