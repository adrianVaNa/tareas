var data = $('#respuesta').html();

function registrarse(){
	var reg= "<h2>Registrarse</h2>"+
			"<form method='POST' action='api/registrarse/nuevo' id='nuevoUsrId'>"+
				"<p>Usuario<input type='text' name='usuario' id='usr_id'></p>"+
				"<p>Contraseña<input type='password' name='contrasena' id='pass_id'></p>"+
				"<p>Nombre<input type='text' name='nombre' id='nom_id'></p>"+
				"<p>Correo<input type='email' name='correo' id='corr_id'></p>"+
				"<input type='button' value='Enviar' onclick='nuevoUsr()'>"+
			"</form>";
	$('#respuesta').html(reg)
}

function nuevoUsr(){
	$.ajax({
		url: 'api/registrarse/nuevo',
		type: 'POST',
		data: $('#nuevoUsrId').serialize(),
		success: function(data){
			alert('Nuevo usuario registrado ');
			location.href ='/tareas';
		}
	});
}

function login(){
	$.ajax({
		url: 'api/login',
		type: 'POST',
		data: $('#formId').serialize(),
		success: function(data){
			if(data=='Error'){
				alert(data+', usuario no valido');
			}
			else{
				alert('Ingresó, bienvenido: '+ data);
				tareas();
			}
		}
	});
}

function logout(){
	$.ajax({
		url: 'api/salir',
		type: 'GET',
		data: data,
		success: function(data){
			alert('Salió del sistema, hasta luego '+ data);
			location.href ='/tareas';
		}
	});
}

function nuevaTarea(){
	$.ajax({
		url: 'api/tareas/nuevaTarea',
		type: 'POST',
		data: $('#nuevaTareaId').serialize(),
		success: function(data){
			//$('#respuesta').html(data);
			tareas();
		}
	});
}

function editaTareas(){
	var datosTabla = '';
	$.ajax({
		url: 'api/tareas',
		type: 'GET',
		data: data,
		dataType: 'json',
		success: function(data){
			$('#respuesta').html('<h2>Tareas</h2><a href="#" onclick="tareas()">Volver</a>');
			$('#respuesta').append('<table><form method="POST" id="editaTareaId">');
			$.each(data, function(){
				datosTabla+='<tr><td> "'+this['texto']+'" </td>'+
				'<td><input type="button" value="Borrar" onclick="borrarTarea('+this['id']+')"></td></tr>';
			});
			$('#respuesta').append(datosTabla);
			$('#respuesta').append('</form></table>');
		}
	});
}

function borrarTarea(idTarea){
	$.ajax({
		url: 'api/tareas/borrar/'+idTarea,
		type: 'DELETE',
		data: data,
		dataType: 'html',
		success: function(data){
			//$('#respuesta').html(data);
			editaTareas();
		}
	});
}

function tareas(){
	var formNuevo = '<form method="POST" id="nuevaTareaId">'+
		'<input type="text" name="tarea">'+
		'<input type="button" value="Agregar" onclick="nuevaTarea()"></form>';
	var datosTabla = '';
	$.ajax({
		url: 'api/tareas',
		type: 'GET',
		data: data,
		dataType: 'json',
		success: function(data){
			$('#respuesta').html('<h2>Tareas</h2><a href="#" onclick="editaTareas()"> Editar </a> | <a href="/tareas" onclick="logout()"> Salir </a><table>');
			$.each(data, function(){
				if(this['realizada']=='1'){
					datosTabla += '<tr><td><input type="checkbox" onclick="editaRealizada('+this['id']+')" checked></td> <td>'+this['texto']+'</td></tr>';
				}
				else{
					datosTabla += '<tr><td><input type="checkbox" onclick="editaRealizada('+this['id']+')"></td> <td>'+this['texto']+'</td></tr>';
				}
			});
			$('#respuesta').append(datosTabla);
			$('#respuesta').append('</table>');
			$('#respuesta').append(formNuevo);
		}
	});
}

function editaRealizada(idTarea){
	$.ajax({
		url: 'api/tareas/editaRealizada/'+idTarea,
		type: 'PUT',
		data: data,
		dataType: 'html',
		success: function(data){
			//$('#respuesta').html(data);
			tareas();
		}
	});
}
