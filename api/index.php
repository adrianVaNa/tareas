<?php
include 'conexion.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
 
$app->get('/', function() use($app) {
    $app->response->setStatus(200);
	echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
	echo '<h1 style="text-align:center;">Regresando...</h1>';
}); 

$app->post('/login',function() use($app){
	$app->response->setStatus(200);
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
			$_SESSION['nombre']=$id_usuario['nombre'];
		}
		else{
			echo "Error";
		}
		
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
});

$app->get('/login',function() use($app){
	echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
	echo '<h1 style="text-align:center;">Regresando...</h1>';
});

$app->get('/salir',function() use($app){
	session_start();
	try{
		echo $_SESSION['nombre'];
		session_unset();
		session_destroy();
	}
	catch(ErrorException $e){
		echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
		echo '<h1 style="text-align:center;">Inicie sesión</h1>';
		echo '<h1 style="text-align:center;">Regresando...</h1>';
	}
});

$app->post('/registrarse/nuevo',function() use($app){
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
});

$app->get('/registrarse/nuevo',function() use($app){
	echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../../">';
	echo '<h1 style="text-align:center;">Regresando...</h1>';
});

$app->get('/socios',function() use($app){
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
});

$app->get('/tareas',function() use($app){
	session_start();
	try{
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
	catch(ErrorException $e2){
		echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
		echo '<h1 style="text-align:center;">Inicie sesión</h1>';
		echo '<h1 style="text-align:center;">Regresando...</h1>';
	}
});

$app->post('/tareas/nuevaTarea',function() use($app){
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
});

$app->delete('/tareas/borrar/:idTarea',function($idTarea) use($app){
	$sql = "DELETE FROM tareas WHERE id='".$idTarea."' ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd=null;
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
});

$app->put('/tareas/editaRealizada/:idTarea',function($idTarea) use($app){
	$sql = "UPDATE tareas SET realizada = !realizada WHERE id = ".$idTarea." ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd = null;
	}catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
});
 
$app->run();


?>