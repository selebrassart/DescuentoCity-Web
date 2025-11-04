<?php

session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Restablecer Contraseña - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
 <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Restablecer contraseña</h1>

                <form action="/controllers/contraseñaController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="tu@email.com" required>
                    </div>
                    
                   
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" name="enviar">Enviar</button>
                    </div>
                     <p>¿No tiene cuenta? <a href="/views/auth/registro.php" class="text-primary">  Crear una
                 </a></p>
                  
                </form>
            </div>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>
</body>
