<?php

//session_start();
session_destroy();

// $errors = [];

if ($_POST) {
  // Lee los usuarios que hay en la base.
  $usuarios = get_json('./json/users.json');

  // Busca el usuario logueado si corresponde, sino retorna 'false'.
  $usuario = select_user($usuarios, $_POST['email'], $_POST['password']);

  // Si existe el usuario...
  if ($usuario) {
      // Inicia sesion y guarda en $_SESSION todos los datos del usuario logueado.
      session_start();
      $_SESSION['usuario'] = $usuario;    // Le asigna a $_SESSION['usuario] el valor de usuario (que devolvio la funcion de busqueda).

      // Verifica cantidades y agrega al array cuantos items tiene en favoritos y en carrito (integer)
      $us_item_qty = [
        'qty_fav' => (int)items_cart_fav('json/favorites.json', $_SESSION['usuario']['id']),
        'qty_cart' => (int)items_cart_fav('json/carts.json', $_SESSION['usuario']['id'])
      ];

      // Se añade al array del ususario logueado
      $_SESSION['usuario'][] = $us_item_qty;  //Posicion [0] dentro del array usuario

      //var_dump($_SESSION['usuario']);
      //die();

      // Verifica si vino un pedido por POST (agregar al carrito o a favoritos), sino reenvia a home
      if ( isset($_GET['cat']) && isset($_GET['id']) ) {
        header('location: ?view=product&cat='.$_GET['cat'].'&id='.$_GET['id']);
      } else {
        header('location: ?view=home'); // Envia entonces al home.
      }

  } else {
      $error = "Los datos son incorrectos :(";
  }

}


?>

<div class="container" id="login">

  <?php require 'require/header.php'; ?>

  <div class="content-wrap">

    <div class="form-login">
      <form action="" class="formulario" method="post">

        <p class="text-danger"><?= $error ?? '' ?></p>

        <div class="form-group">
          <label for="email">Email :</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="Email" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Password :</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Log in</button>
          <a class="olvido-pass" href="#">Olvidaste tu contraseña?</a>
        </div>
        <div class="form-group">
          <div class="form-check">
            <label class="form-check-label" for="gridCheck">
              <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" checked>
              <i>Recordarme</i>
            </label>
          </div>
        </div>

      </form>
    </div>
  </div>

  <?php require ('require/footer.php'); ?>

</div>