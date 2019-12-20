<?php

//session_start();
session_destroy();

// Lee los estados del pais
$states = get_json('./json/arg-states.json');

// Inicio un array vacio
$errors = [];

// Persistencia de Datos
$fname = "";
$lname = "";
$email1 = "";
$email2 = "";
$user_type = "";
$gender = "";
$phone = "";
$address1 = "";
$city = "";
$state = "";
$zip = "";
$avatar ="";

if ($_POST) {

    // Persistencia de Datos
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email1 = $_POST['email1'];
    $email2 = $_POST['email2'];
    $user_type = null;
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address1 = $_POST['address1'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $avatar = $_FILES['avatar'];

    // Verifica si el email tiene el formato correcto
    if (! filter_var($_POST['email1'], FILTER_VALIDATE_EMAIL)) {
      $errors['email1'] = "El email no es valido !";
    }
    // Verifica que coincidan las 2 contraseñas
    if ($_POST['password'] !== $_POST['password_confirmation']) {
        $errors['password'] = "No coinciden las contraseñas !";
    }

    // Lee los usuarios que hay en la base
    $users = get_json('json/users.json');

    // Si no hay errores, continua...
    if ( empty($errors) ) {

      if ( !verify_registered_email($users, $_POST['email1']) ) {
        
        // Genera un codigo aleatorio unico e irrepetible
        $code = random_id('USER',$users);

        // Cuento cantidad de usuarios previos
        $id = count($users);

        // Establece el formato del array guardado
        $user = [
            'id' => $id++,
            'user_type' => 1,  // Tipo de usuario Customer por defecto
            'code' => $code,
            'firstname' => $_POST['fname'],
            'lastname' => $_POST['lname'],
            'email1' => $_POST['email1'],
            'email2' => $_POST['email2'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'gender' => $_POST['gender'],
            'phone' => $_POST['phone'],
            'address1' => $_POST['address1'],
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'zip' => $_POST['zip'],
            'avatar' => upload_file('avatar', 'uploads', 'MD5', null),
            'created_at' => time(),  // Guarda los segundos desde la fecha Unix (01 Enero de 1970)
            'updated_at' => null,
        ];

        // Agregar al array.
        $users[] = $user;

        // Guarda los usuarios json.
        save_json($users,'./json/users.json');

        // Redirecciona a login.php
        redirect('login');

      } else {
        $errors['email1'] = "El email ya existe !";
      }

    }
    
}
?>


<div class="container" id="registro">

  <?php require 'require/header.php'; ?>

  <div class="content-wrap">

    <div class="form-register">
      <form action="" class="formulario" method="post" enctype="multipart/form-data">

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inputFName">Nombre :</label>
            <input type="text" name="fname" value="<?= $fname ?>" id="fname" class="form-control" placeholder="Nombre" required autofocus>
          </div>
          <div class="form-group col-md-6">
            <label for="inputLName">Apellido :</label>
            <input type="text" name="lname" value="<?= $lname ?>" id="lname" class="form-control" placeholder="Apellido" required>
          </div>
          <div class="form-group col-md-6">
            <label for="inputEmail1">Email Principal :</label>
            <input type="email" name="email1" value="<?= $email1 ?>" id="email1" class="form-control" placeholder="Email" required>
            <p class="text-danger"><?= $errors['email1'] ?? '' ?></p>
          </div>
          <div class="form-group col-md-6">
            <label for="inputEmail2">Email Alternativo :</label>
            <input type="email" name="email2" value="<?= $email2 ?>" id="email2" class="form-control" placeholder="Email Alternativo">
          </div>
          <div class="form-group col-md-6">
            <p>
              <label for="inputPassword">Password :</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Password"
                pattern="[A-Za-z0-9]{4,15}" title="El password debe tener entre 4 y 10 caracteres de longitud, 
            donde cada carácter puede ser una letra mayúscula, minúscula o un dígito" required>
            </p>
            <p class="text-danger"><?= $errors['password'] ?? '' ?></p>
          </div>
          <div class="form-group col-md-6">
            <label for="inputPassword_Confirmation">Confirmacion Password :</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
              placeholder="Confirmar Password" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inputUser">Teléfono :</label>
            <input type="text" name="phone" value="<?= $phone ?>" id="phone" class="form-control">
          </div>
          <div class="form-group col-md-6">
            <label for="gender">Género :</label>
            <select name="gender" id="gender" class="form-control" required>
              <?php if ($gender == 'Masculino') : ?>
              <option value="Femenino">Femenino</option>
              <option selected value="Masculino">Masculino</option>
              <option value="Otro">Otro</option>
              <?php elseif ($gender == 'Femenino') : ?>
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
          <label for="inputAddress1">Direccion :</label>
          <input type="text" name="address1" value="<?= $address1 ?>" id="address1" class="form-control" placeholder="1234 Main St">
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inputCity">Ciudad :</label>
            <input type="text" name="city" value="<?= $city ?>" id="city" class="form-control">
          </div>
          <div class="form-group col-md-4">
            <label for="state">Estado :</label>
            <select name="state" id="state" class="form-control">
              <?php foreach ($states as $provincia => $prov) : ?>
                <?php if ($_POST['state'] == $provincia) : ?>
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
            <input type="text" name="zip" value="<?= $zip ?>" id="zip" class="form-control">
          </div>
        </div>
        
          <div class="form-group col-md-6" id="avatar-file">
            <label for="">Subi tu Avatar :</label>
            <input type="file" name="avatar" accept="image/*">
          </div>
        
        <div class="form-check">
          <label class="form-check-label" for="gridCheck">
            <input class="form-check-input" type="checkbox" name="accept" id="accept" value="1" required>
            <i>Acepto las condiciones y términos</i>
          </label>
        </div>
    </div>
    <!-- <button type="submit" class="btn btn-primary">Enviar</button> -->
    <div class="register-boton">
      <input type="image" src="img/img.register/PRESSSTAR.png" alt="press start" class="form-btn">
    </div>

    </form>
  </div>

  <?php require 'require/footer.php'; ?>

</div>