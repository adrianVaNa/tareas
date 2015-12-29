<!DOCTYPE html>
<!--
Adrián Vázquez Navarrete
-->
<html>
    <head>
    	<title>Tareas</title>
    	<meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/principal.css">
		<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script src="js/principal.js">

		</script>
    </head>
    <body>
	
    	<header><h1>Su lista de tareas online</h1></header>
    	
    	<section class="contenido" id='respuesta'>
			<h2>Ingresar</h2>
			<form method='POST' action='api/login' id="formId">
			<p>Usuario <input type='text' name='usuario'></p>
			<p>Contraseña <input type='password' name='contrasena'></p>
			<input type="button" value="Ingresar" onclick="login()">
			</form>
			<p id='1'><a href="#" onclick="registrarse()"> Registrarse </a></p>
    	</section>
		
		<!-- <footer><p>© Tareas online. 2015</p></footer> -->
        
    </body>
</html>
    	