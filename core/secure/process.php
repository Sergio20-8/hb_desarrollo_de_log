<?php
ini_set('display_error',1);
ini_set('display_startup_error',1);
include('inc/funciones.inc.php');
include('secure/ips.php');

$metodo_permitido = "POST";
$archivo = "../logs/log.log";
$dominio_autorizado = "localhost";
$ip = ip_in_ranges($_SERVER["REMOTE_ADDR"],$rango);
$txt_usuario_autorizado = "admin";
$txt_password_autorizado = "admin";

if(array_key_exists("HTTP_REFERER",$_SERVER)){



    if(strpos($_SERVER["HTTP_REFERER"],$dominio_autorizado)){
        if($ip == true){
            if($_SERVER["REQUEST_METHOD"] == $metodo_permitido){
                $valor_campo_usuario = ((array_key_exists("txt_user",$_POST))? htmlspecialchars(stripslashes(trim($_POST["txt_user"])),ENT_QUOTES) : "");
                $valor_campo_password = ((array_key_exists("txt_pass",$_POST))? htmlspecialchars(stripslashes(trim($_POST["txt_pass"])),ENT_QUOTES) : "");
                
                if(($valor_campo_usuario != "" || strlen($valor_campo_usuario) > 0) && ($valor_campo_password != "" || strlen($valor_campo_password) > 0)){
                    $usuario = preg_match('/^[a-zA-Z0-9]{1,10}+$/',$valor_campo_usuario);
                    $password = preg_match('/^[a-zA-Z0-9]{1,10}+$/',$valor_campo_password);

                    if($usuario !== false and $usuario !== 0 and $password !== false and $password !== 0){


                        if ($valor_campo_usuario === $txt_usuario_autorizado and $valor_campo_password === $txt_password_autorizado) {
                            echo("HOLA MUNDO");
                            crear_editar_log($archivo, "EL CLIENTE INICIÓ SESIÓN SATISFACTORIAMENTE",1,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                            # code...
                        } else {
                            crear_editar_log($archivo, "CREDENCIALES INCORRECTAS ENVIADAS HACIA //$_SERVER[HTTP_POST]$_SERVER[HTTP_REQUEST_URI]",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
                            header("HTTP/1.1 301 Moved Permanently");
                            header("Location: ../?status=7");
                        }
                        
                    } else {
                        crear_editar_log($archivo, "ENVIO DE DATOS DEL FORMULARIO CON CARACTERES NO SOPORTADOS",3,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: ../?status=6");
                    }

                }else{
                    crear_editar_log($archivo, "ENVIO DE CAMPOS VACÍOS AL SERVIDOR",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../?status=5");
                }
            }else{
                crear_editar_log($archivo, "ENVIO DE MÉTODO NO AUTORIZADO",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ../?status=4");
            }
        }else{
            crear_editar_log($archivo, "DIRECCIÓN IP NO AUTORIZADA",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ../?status=3");
        }
    }else{
        crear_editar_log($archivo, "HA INTENTADO SUPLANTAR UN REFERER QUE NO ESTÁ AUTORIZADO",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ../?status=2");
    }
}else {
    crear_editar_log($archivo, "EL USUARIO HA INTENTADO INGRESAR AL SISTEMA DE UNA MANERA INCORRECTA",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$SERVER["HTTP_USER_AGENT"]);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ../?status=1");
}
?>