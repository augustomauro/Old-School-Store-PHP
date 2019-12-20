<?php

// Si no hay sesion iniciada da ERROR
if (! $_SESSION ) {
  header('location: ?view=403');
  //header('location: ./?view=home');
  die();
}

// Inicio un array vacio.
$errors = [];

// Inicio un contador de cambios
$changes = 0;

// Lee los estados del pais
$provincias = get_json('./json/arg-states.json');

// Selecciono el usuario cuyo id corresponda con el logueado (un array)
// $usuario = $usuarios[$_SESSION['usuario']['id']];

// Si se presiona el boton 'Habilitar Edicion' ($_POST['disabled']) 
// le asigna un valor de '1' a la variable llamada 'disabled' 
$disabled = $_POST['disabled'] ?? 1;


if ($_POST) {
    // Si se presiona el boton 'Enviar' ($_POST['editar'])...
    // Reemplazamos solo el usuario logueado con los nuevos valores del form (POST)
    if ( isset($_POST['editar']) ) {

      $pattern = '[A-Za-z0-9]{4,15}';

      // Verifica que coincidan las 2 contraseñas
      if ($_POST['password'] !== $_POST['password_confirmation']) {
        $errors['password'] = "No coinciden las contraseñas !";
      }

      // Si no hay errores, continua...
      if (empty($errors)) {

        // Verifica posicion original del usuario dentro de users.json
        // Lee los usuarios que hay en la base
        $usuarios = get_json('./json/users.json');
        $i = 0;
        foreach ($usuarios as $usuario) {
          if ( $usuario['id'] === $_SESSION['usuario']['id'] ) {
            $pos = $i;
          }
          $i++;
        }

        // Si el password en el campo input cambia, entonces sobreescribo la password anterior
        if ($_POST['password'] != $_SESSION['usuario']['password']) {
          $usuarios[$pos]['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
          $changes++;
        }

        // Nombre, Apellido, Email1, Email2
        if ($usuarios[$pos]['firstname'] !== $_POST['fname']) {
          $usuarios[$pos]['firstname'] = $_POST['fname'];
          $changes++;
        }
        if ($usuarios[$pos]['lastname'] !== $_POST['lname']) {
          $usuarios[$pos]['lastname'] = $_POST['lname'];
          $changes++;
        }
        if ($usuarios[$pos]['email1'] !== $_POST['email1']) {
          $usuarios[$pos]['email1'] = $_POST['email1'];
          $changes++;
        }
        if ($usuarios[$pos]['email2'] !== $_POST['email2']) {
          $usuarios[$pos]['email2'] = $_POST['email2'];
        }

        // Sexo
        if ($usuarios[$pos]['gender'] !== $_POST['gender']) {
          $usuarios[$pos]['gender'] = $_POST['gender'];
          $changes++;
        }
        
        // Teléfono
        if ($usuarios[$pos]['phone'] !== $_POST['phone']) {
          $usuarios[$pos]['phone'] = $_POST['phone'];
          $changes++;
        }

        // Direccion
        if ($usuarios[$pos]['address1'] !== $_POST['address1']) {
          $usuarios[$pos]['address1'] = $_POST['address1'];
          $changes++;
        }

        // Ciudad, Estado, Cod Postal, Usuario
        if ($usuarios[$pos]['city'] !== $_POST['city']) {
          $usuarios[$pos]['city'] = $_POST['city'];
          $changes++;
        }
        if ($usuarios[$pos]['state'] !== $_POST['state']) {
          $usuarios[$pos]['state'] = $_POST['state'];
          $changes++;
        }
        if ($usuarios[$pos]['zip'] !== $_POST['zip']) {
          $usuarios[$pos]['zip'] = $_POST['zip'];
          $changes++;
        }
        
        // Si se adjunta un nuevo archivo de imagen ('size' != 0), elimina el archivo anterior y sube uno nuevo
        if (! $_FILES['avatar']['size'] == 0) {
          unlink($usuarios[$pos]['avatar']);
          $usuarios[$pos]['avatar'] = upload_file('avatar','uploads','MD5');
          $changes++;
        }

        // Si hubo algun cambio ($changes != 0) graba el valor 'updated_at'
        if ($changes != 0) {
          $usuarios[$pos]['updated_at'] = time();
        }

        // Grabamos la nueva base a json (reemplaza la anterior)
        save_json($usuarios,'./json/users.json');
        // Desloguea al usuario
        redirect('logout');

      }

    }

    if ( isset($_POST['cancelar']) ) {
      $disabled = 1;
      $pattern = '';
    }

}

?>

<div class="container" id="perfilUs">

  <?php require 'require/header.php'; ?>

    <div class="content-wrap">

      <div class="usuario">

        <div class="usuario-perfil">

          <!-- Determina el Genero para la Bienvenida -->
          <?php if ( $_SESSION['usuario']['gender'] == 'Femenino') : ?>
          <?php $mensaje = 'Bienvenida, '; ?>
          <?php elseif ( $_SESSION['usuario']['gender'] == 'Masculino') : ?>
          <?php $mensaje = 'Bienvenido, '; ?>
          <?php else : ?>
          <?php $mensaje = 'Hola, '; ?>
          <?php endif; ?>

          <!-- Determina el sexo para la Bienvenida. Si el usuario inicia session, mostrar el siguiente titulo -->
          <?php if( isset($_SESSION['usuario']['email1']) ) : ?>
          <h3><?= $mensaje . $_SESSION['usuario']['firstname'] . ' ' . $_SESSION['usuario']['lastname']?></h3>
          <h5>Su Cod Usuario es el Nº <?= $_SESSION['usuario']['code'] ?> <small> <?= $_SESSION['usuario']['created_at'] ? ' | Registro el ' . gmdate('d/m/y', $_SESSION['usuario']['created_at']) : null ?> </small></h5>

          <?php else : ?>
          <!-- Sino inicia, mostrar... -->
          <h3>Bienvenido/a, Usuario/a</h3>
          <?php endif; ?>

          <h6>Editar Perfil</h6>

          <a href=""><img src="<?= $source ?>"></a>
          <!-- <a href="#"><img src="img/usuariomario.png" alt=""></a> -->

        </div>

        <div class="barra-usuario">
          <nav>
            <ul>
              <li><a href="#">Mis Compras</a></li>
              <li><a href="?view=cart">Mi Carrito</a></li>
              <li><a href="?view=favorites">Mis Favoritos</a></li>
            </ul>
          </nav>
        </div>

        <div class="datos-usuario">
          <form class="formulario" method="post" action="" enctype="multipart/form-data">

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputName">Nombre :</label>
                <input type="text" name="fname" id="fname" class="form-control"
                  value="<?= $_SESSION['usuario']['firstname'] ?>" <?= $disabled ? 'readonly' : '' ?>>
              </div>
              <div class="form-group col-md-6">
                <label for="inputLName">Apellido :</label>
                <input type="text" name="lname" id="lname" class="form-control"
                  value="<?= $_SESSION['usuario']['lastname'] ?>" <?= $disabled ? 'readonly' : '' ?>>
              </div>
              <div class="form-group col-md-6">
                <label for="inputEmail4">Email Principal :</label>
                <input type="email" name="email1" id="email1" class="form-control"
                  value="<?= $_SESSION['usuario']['email1'] ?>" <?= $disabled ? 'readonly' : '' ?>>
              </div>
              <div class="form-group col-md-6">
                <label for="inputEmail4">Email Alternativo :</label>
                <input type="email" name="email2" id="email2" class="form-control"
                  value="<?= $_SESSION['usuario']['email2'] ?>" <?= $disabled ? 'readonly' : '' ?>>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <p>
                  <label for="">Password :</label>
                  <input type="password" class="form-control" name="password" pattern="<?= $pattern ?>" title="El password debe tener entre 4 y 10 caracteres de longitud, 
                donde cada carácter puede ser una letra mayúscula, minúscula o un dígito"
                    value="<?= $_SESSION['usuario']['password'] ?>" <?= $disabled ? 'readonly' : '' ?>>
                </p>
              </div>
              <div class="form-group col-md-6">
                <p>
                  <label for="">Confirmar Password :</label>
                  <input type="password" class="form-control" name="password_confirmation" pattern="<?= $pattern ?>"
                    title="El password debe tener entre 4 y 10 caracteres de longitud, 
                donde cada carácter puede ser una letra mayúscula, minúscula o un dígito"
                    value="<?= $_SESSION['usuario']['password'] ?>" <?= $disabled ? 'readonly' : '' ?>>
                </p>
              </div>
              <p class="text-danger"><?= $errors['password'] ?? '' ?></p>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputUser">Teléfono :</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?= $_SESSION['usuario']['phone'] ?>"
                  <?= $disabled ? 'readonly' : '' ?>>
              </div>
              <div class="form-group col-md-6">
              <label for="gender">Género :</label>
                <select name="gender" id="gender" class="form-control" <?= $disabled ? 'disabled' : '' ?>>
                  <?php if ($_SESSION['usuario']['gender'] == 'Masculino') : ?>
                  <option value="Femenino">Femenino</option>
                  <option selected value="Masculino">Masculino</option>
                  <option value="Otro">Otro</option>
                  <?php elseif ($_SESSION['usuario']['gender'] == 'Femenino') : ?>
                  <option selected value="Femenino">Femenino</option>
                  <option value="Masculino">Masculino</option>
                  <option value="Otro">Otro</option>
                  <?php else : ?>
                  <option value="Femenino">Femenino</option>
                  <option value="Masculino">Masculino</option>
                  <option selected value="Otro">Otro</option>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputAddress">Direccion :</label>
              <input type="text" name="address1" id="address1" class="form-control"
                value="<?= $_SESSION['usuario']['address1'] ?>" <?= $disabled ? 'readonly' : '' ?>>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputCity">Ciudad :</label>
                <input type="text" name="city" id="city" class="form-control" value="<?= $_SESSION['usuario']['city'] ?>"
                  <?= $disabled ? 'readonly' : '' ?>>
              </div>
              <div class="form-group col-md-4">
                <label for="inputState">Estado :</label>
                <select name="state" id="state" class="form-control" <?= $disabled ? 'disabled' : '' ?>>
                  <?php if ($_SESSION['usuario']['state'] === '') : ?>
                    <option value=""></option>
                  <?php endif; ?>
                    <?php foreach ($provincias as $provincia => $prov) : ?>
                    <?php if ($_SESSION['usuario']['state'] == $provincia) : ?>
                      <option value="<?= $provincia ?>" selected>
                        <?= $prov ?>
                      </option>
                    <?php else : ?>
                      <option value="<?= $provincia ?>">
                        <?= $prov ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="inputZip">C.P. :</label>
                <input type="text" name="zip" id="zip" class="form-control" value="<?= $_SESSION['usuario']['zip'] ?>"
                  <?= $disabled ? 'readonly' : '' ?>>
              </div>

              <div class="form-group col-md-6" id="avatar-file">
                <label for="">Cambiá tu Avatar :</label>
                <input type="file" name="avatar" accept=".jpg,.jpeg,.png," <?= $disabled ? 'disabled' : '' ?>>
              </div>

            </div>

            <div class="boton-guardar">
              <?php if (!$disabled): ?>
              <div class="form-group">
                <button name="editar" class="btn btn-sm btn-success">Guardar</button>
                <button name="cancelar" class="btn btn-sm btn-danger">Cancelar</button>
              </div>
              <?php else: ?>
              <!--  <input type="hidden" name="disabled" value="0"> -->
              <button name="disabled" class="btn btn-warning">Editar</button>
              <?php endif ?>
              <br>
              <br>
              <a href="?action=logout">Cerrar Sesion</a>
            </div>

            <!-- <div class="boton-guardar">
            <button type="submit" class="btn btn-outline-success">Guardar datos</button>
          </div> -->

          </form>
        </div>

      </div>
    </div>

  <?php require 'require/footer.php';  ?>

</div>