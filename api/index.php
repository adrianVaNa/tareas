<?php
include 'conexion.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
 
$app->get('/', function() use($app) {
    $app->response->setStatus(200);
	session_start();
	if($_SESSION['id']){
		echo $_SESSION['id'];
		session_unset();
		session_destroy();
	}
}); 
$app->post('/login','login');
$app->post('/registrarse/nuevo','nuevoSocio');
$app->get('/socios','getSocios');
$app->get('/tareas','getTareas');
$app->post('/tareas/nuevaTarea','nuevaTarea');
$app->delete('/tareas/borrar/:idTarea','borraTarea');
$app->put('/tareas/editaRealizada/:idTarea','editaRealizada');
 
$app->run();

function getSocios(){
	$sql = "SELECT * FROM socios;";	
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql); 
		$socios = $stmt->fetchAll(PDO::FETCH_OBJ);
		$bd = null;
		echo '{"socios": ' .json_encode($socios). '}';
		echo count($socios);
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function getTareas(){
	session_start();
	$sql = "SELECT * FROM tareas WHERE usuario= '".$_SESSION['id']."' ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$tareas = $stmt->fetchAll(PDO::FETCH_OBJ);
		$bd = null;
		echo json_encode($tareas);
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function nuevaTarea(){
	$request = \Slim\Slim::getInstance()->request();
	$paramTarea = $request->params('tarea');
	session_start();
	$sql = "INSERT INTO tareas (usuario,texto) values (".$_SESSION['id'].", :tarea) ";
	try{
		$bd = conectaBD();
		$stmt = $bd->prepare($sql);
		$stmt->bindParam("tarea", $paramTarea);
		$stmt->execute();
		$bd=null;
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}
// UPDATE `tareas` SET `realizada`= !realizada WHERE id = '1'
function editaRealizada($idTarea){
	$sql = "UPDATE tareas SET realizada = !realizada WHERE id = ".$idTarea." ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd = null;
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function borraTarea($idTarea){
	$sql = "DELETE FROM tareas WHERE id='".$idTarea."' ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd=null;
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function nuevoSocio(){
	$request = \Slim\Slim::getInstance()->request();
	$paramUsuario = $request->params('usuario');
	$paramContrasena = $request->params('contrasena');
	$paramNombre = $request->params('nombre');
	$paramCorreo = $request->params('correo');
	$sql = "INSERT INTO socios (usuario,contrasena,nombre,correo) VALUES(:usuario ,:contrasena, :nombre, :correo)";
	try{
		$bd = conectaBD();
		$stmt = $bd->prepare($sql);
		$stmt->bindParam("usuario", $paramUsuario);
		$stmt->bindParam("contrasena", $paramContrasena);
		$stmt->bindParam("nombre", $paramNombre);
		$stmt->bindParam("correo", $paramCorreo);
		$stmt->execute();
		$bd=null;
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function login(){
	$request = \Slim\Slim::getInstance()->request();
	$paramUsuario = $request->params('usuario');
	$paramContrasena = $request->params('contrasena');
	$sql = "SELECT id,nombre FROM socios WHERE usuario = :usuario AND contrasena= :contrasena ";
	try{
		$bd = conectaBD();
		$stmt = $bd->prepare($sql);
		$stmt->bindParam("usuario", $paramUsuario);
		$stmt->bindParam("contrasena", $paramContrasena);
		$stmt->execute();
		$res = $stmt->fetchObject();
		$bd=null;
		$res = json_encode($res);
		$id_usuario = json_decode($res,true);
		if(count($id_usuario['nombre'])>=1 ){
			echo $id_usuario['nombre'];
			session_start();
			$_SESSION['id']=$id_usuario['id'];
		}
		else{
			echo "Error";
		}
		
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

?>