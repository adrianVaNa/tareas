<?php
include 'conexion.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
 
$app->get('/', function() use($app) {
    $app->response->setStatus(403);
	echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
	echo '<h1 style="text-align:center;">Regresando...</h1>';
});

$app->post('/login',function() use($app){
	$request = $app->request->getBody();
	$arr = json_decode($request,true);
	$sql = "SELECT id,nombre FROM socios WHERE usuario = '".$arr[0]["value"]."' AND contrasena = '".$arr[1]["value"]."'; ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$res = $stmt->fetchAll();
		$bd=null;
		if( $stmt->rowCount() >=1 ){
			session_start();
			$_SESSION['id']=$res[0]['id'];
			$_SESSION['nombre']=$res[0]['nombre'];
			echo json_encode($res);
			$app->response->setStatus(200);
		}
		else{
			$app->response->setStatus(401);
			echo '[{"error":"true" , "texto":"Usuario no válido" }]';
		}
		
	}catch(PDOException $e){
		$app->response->setStatus(400);
		echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
	}
});

$app->get('/login',function() use($app){
	$app->response->setStatus(403);
	echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
	echo '<h1 style="text-align:center;">Regresando...</h1>';
});

$app->get('/salir',function() use($app){
	$app->response->setStatus(200);
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
	$request = $app->request->getBody();
	$arr = json_decode($request,true);
	$sql = "INSERT INTO socios (usuario,contrasena,nombre,correo) VALUES('".$arr[0]["value"]."' ,'".$arr[1]["value"]."', '".$arr[2]["value"]."', '".$arr[3]["value"]."')";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		if($stmt) $app->response->setStatus(201); //Devuelve mensaje 201 al agregar el socio
		else $app->response->setStatus(401);	// si no devuelve el código de error
		$bd=null;
	}catch(PDOException $e){
		$app->response->setStatus(400);
		echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
	}
});

$app->get('/registrarse/nuevo',function() use($app){
	$app->response->setStatus(403);
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
		$app->response->setStatus(200);
		echo '[{"socios": ' .json_encode($socios). ', "cant_usuarios": ' .count($socios). ' }]';
	}
	catch(PDOException $e){
		$app->response->setStatus(400);
		echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
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
			$app->response->setStatus(200);

			echo json_encode($tareas);
		}
		catch(PDOException $e){
			$app->response->setStatus(400);
			echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
		}
	}
	catch(ErrorException $e2){
		$app->response->setStatus(403);
		echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL=../">';
		echo '<h1 style="text-align:center;">Inicie sesión</h1>';
		echo '<h1 style="text-align:center;">Regresando...</h1>';
	}
});

$app->post('/tareas/nuevaTarea',function() use($app){
	$request = $app->request->getBody();
	$arr = json_decode($request,true);
	session_start();
	$sql = "INSERT INTO tareas (usuario,texto) values ('".$_SESSION['id']."', '".$arr[0]["value"]."') ";
	//echo $sql;
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd=null;
		$app->response->setStatus(201);
	}catch(PDOException $e){
		$app->response->setStatus(400);
		echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
	}
});

$app->delete('/tareas/borrar/:idTarea',function($idTarea) use($app){
	$sql = "DELETE FROM tareas WHERE id='".$idTarea."' ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd=null;
		$app->response->setStatus(204);
	}catch(PDOException $e){
		$app->response->setStatus(400);
		echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
	}
});

$app->put('/tareas/editaRealizada/:idTarea',function($idTarea) use($app){
	$sql = "UPDATE tareas SET realizada = !realizada WHERE id = ".$idTarea." ";
	try{
		$bd = conectaBD();
		$stmt = $bd->query($sql);
		$bd = null;
		$app->response->setStatus(204);

	}catch(PDOException $e){
		$app->response->setStatus(400);
		echo '[{"error":"true" , "texto":'. $e->getMessage() .'}]';
	}
});
 
$app->run();


?>