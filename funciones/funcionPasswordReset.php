<?php
function generar_y_guardar_token($conexion, $user_id) {
    
    // Generar token legible (el que se envía al usuario)
    $token_full = bin2hex(random_bytes(16)); 
    $token_code = strtoupper(substr($token_full, 0, 8)); // Código de 8 caracteres.
    $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hora de expiración

    // CREAR EL HASH SEGURO para almacenar
    $token_hash = password_hash($token_code, PASSWORD_DEFAULT); 

    //  Limpiar tokens antiguos de ESTE usuario
    $delete_old_stmt = $conexion->prepare("DELETE FROM password_resets WHERE idUsuario = ?");
    $delete_old_stmt->bind_param("i", $user_id);
    $delete_old_stmt->execute();
    
    //  Insertar el HASH y la fecha de expiración en la tabla SEPARADA (password_resets)
    $insert_token_stmt = $conexion->prepare("INSERT INTO password_resets (idUsuario, reset_token_hash, reset_token_expire) VALUES (?, ?, ?)");
    // El id es entero (i), el hash es cadena (s) y la fecha es cadena (s)
    $insert_token_stmt->bind_param("iss", $user_id, $token_hash, $expires);
    
    if ($insert_token_stmt->execute()) {
        return $token_code; // Devolvemos el código original para que sea enviado por email.
    }
    
    return false; 
}


function enviar_token_restablecimiento($email, $token_code) {
    
    $destino = $email; 
    $asunto = 'Código de Restablecimiento de Contraseña - DESCUENTO CITY.';
    
    $header = "MIME-Version: 1.0\r\n";
    $header .= "Content-type:text/html; charset=UTF-8\r\n"; 
    $header .= "From: Descuento City <noreply@descuentocity.com>" ."\r\n"; 

    $cuerpo = "
    <html>
        <body>
            <h1>Restablecimiento de Contraseña</h1>
            <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta.</p>
            <p>Utilice el siguiente código de verificación en el formulario:</p>
             <p style='font-size: 24px; font-weight: bold; color: #007bff; padding: 10px; border: 2px solid #007bff; display: inline-block; letter-spacing: 5px; border-radius: 5px;'>
                {$token_code}
            </p>
            <p>Este código es válido por 1 hora. Si usted no solicitó este cambio, por favor ignore este correo.</p>
            
        </body>
    </html>
    ";


    return mail($destino, $asunto, $cuerpo, $header);
}

function generar_y_enviar_token($conexion, $user_id, $email) {
    
    
    $token_code = generar_y_guardar_token($conexion, $user_id); 

    if ($token_code === false) {
        return false; // Falló la bdd, no hay token para enviar
    }
    
    //Llama a la función de envío, pasándole el código que acaba de recibir.
    enviar_token_restablecimiento($email, $token_code); 
    
    return true; 
}

function validar_token_y_cambiar_clave($conexion, $token_code, $new_clave) {
    
    $current_time = date("Y-m-d H:i:s");
    
    //  Consultar TODOS los hashes no expirados para verificar cuál coincide
    $consulta = "SELECT idUsuario, reset_token_hash 
                 FROM password_resets 
                 WHERE reset_token_expire > ?";
    
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("s", $current_time); // Solo comparamos con la fecha actual
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $usuario_id = null;
    $token_hash_usado = null; // Guardamos el hash que coincide para poder borrarlo
    
    //  Iterar sobre los tokens activos y usar password_verify()
    while ($registro = $resultado->fetch_assoc()) {
        $token_hash = $registro['reset_token_hash'];
        
        // Verifica si el código ingresado por el usuario coincide con el hash guardado
        if (password_verify($token_code, $token_hash)) {
            $usuario_id = $registro['idUsuario'];
            $token_hash_usado = $token_hash;
            break; // Token encontrado y verificado
        }
    }
    
    //  Si se encontró un token válido: Actualizar la contraseña
    if ($usuario_id !== null) {
        
        // Hashear la nueva contraseña para la tabla 'usuarios'
        $hashed_clave = password_hash($new_clave, PASSWORD_DEFAULT);
        
        // Actualizar la contraseña 
        $update_clave = "UPDATE usuarios SET claveUsuario = ? WHERE codUsuario = ?";
        $update_stmt = $conexion->prepare($update_clave);
        
        $update_stmt->bind_param("si", $hashed_clave, $usuario_id);
        
        if ($update_stmt->execute()) {
            
            // Elimina el hash usado (UN SOLO USO)
            // Lo borramos usando el hash específico que se usó
            $delete_token = "DELETE FROM password_resets WHERE reset_token_hash = ?";
            $delete_stmt = $conexion->prepare($delete_token);
            $delete_stmt->bind_param("s", $token_hash_usado);
            $delete_stmt->execute();
            
            return true;
        }
        // Error de bdd al actualizar la contraseña
        return "error_actualizar_db"; 
    }
    
    // Si el bucle terminó sin encontrar coincidencia
    return "token_invalido"; 
}
?>