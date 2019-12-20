<?php

// Determinar la cantidad de categorias que EXISTEN en la base de datos--------------------//
$exist_cat = active_categories('QTY_CAT');
//----------------------------------------------------------------------------------------//

if (! isset($_SESSION['usuario'])) {
  // Inicializa variables para cantidades Carrito/Favoritos si no hay sesion iniciada
  //$qty_cart = 0;
  //$qty_fav = 0;
}

// Verificamos si existe una sesion iniciada
if (isset($_SESSION['usuario'])) {

  // Sesion iniciada
  if ($_SESSION['usuario']['avatar'] != null) {
    $source = $_SESSION['usuario']['avatar'];
  } else {
    $source = 'img/avatar/account-100.png';
  }

  // Vuelve a contar cantidades y reescribe los valores del array de items favoritos y carrito (integer)
  //$_SESSION['usuario'][0]['qty_fav'] = (int)items_cart_fav('./json/favoritos.json', $_SESSION['usuario']['id']);
  //$_SESSION['usuario'][0]['qty_cart'] = (int)items_cart_fav('./json/carrito.json', $_SESSION['usuario']['id']);

  // Verifica si hay cantidades añadidas al carrito y/o a favoritos (array que se agrega en el login)
  $qty_cart = $_SESSION['usuario'][0]['qty_cart'];
  $qty_fav = $_SESSION['usuario'][0]['qty_fav'];

  $logon = 'fas fa-sign-out-alt';
  $class = null;
  $perfil = '?view=profile';

} else {
    // Sesion no iniciada
    $source = 'img/avatar/account-100.png';
    $logon = null;
    $class = 'isDisabled';
    $perfil = '#';
    // Inicializa variables para cantidades Carrito/Favoritos si no hay sesion iniciada
    $qty_cart = 0;
    $qty_fav = 0;
  }

?>

<header>
  <section class="barra-inicio">
    <!--<img src="img/logo.png" alt="Old School Store" id="logo">-->
    <nav class="navbar navbar-expand-lg navbar-light">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="true" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse collapse show" id="navbarTogglerDemo01" style="">
      <div id="navbar-brand">
        <a class="navbar-brand" href="?view=home"><img src="img/logo.png" alt="Old School Store" id="logo"></a>
      </div>
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item"><a class="nav-link" href="?action=register"><i class="fas fa-user"></i> Crear Cuenta</a></li>
          <li class="nav-item"><a class="nav-link" href="?action=login"><i class="fas fa-sign-in-alt"></i> Abrir Cuenta</a></li>
          <?php if ( isset($_SESSION['usuario']) ) : ?>
          <li class="nav-item"><a class="nav-link <?= $class ?>" href="#" title="Mis Compras"><i class="fas fa-shopping-bag"></i> Mis Compras</a></li>
          <li class="nav-item nav-link <?= $class ?>"><a href="?view=cart" class="<?= $class ?>" title="Mi Carrito"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
            <span style="color: lightgreen; font-weight: normal">(<?= $qty_cart ?>)</span></li>
          <li class="nav-item nav-link <?= $class ?>"><a href="?view=favorites" class="<?= $class ?>" title="Mis Favoritos"><i class="fa fa-heart" aria-hidden="true"></i></a>
            <span style="color: lightgreen; font-weight: normal">(<?= $qty_fav ?>)</span></li>
          <li class="nav-item"><a href="?view=profile"><img class="<?= $class ?>" src="<?= $source ?>" alt="avatar" id="avatar" title="Mi Perfil"></a></li>
          <li class="nav-item"><a class="nav-link" id="salir-home" href="?action=logout" title="Salir"><i style="font-size: 1rem" class="<?= $logon ?>"></i></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
  </section>

  <section class="categorias-inicio">
    <nav class="navBuscador">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link <?= ($_GET['view'] == 'home' || $_GET['view'] == '') ? 'active' : '' ?>" href="?view=home">Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Categorias</a>
          <div class="dropdown-menu">
            <?php foreach ($exist_cat as $categ) : ?>
              <a class="dropdown-item" href="?view=detail&cat=<?= $categ ?>"><?= $categ ?></a>
            <?php endforeach; ?>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="?view=detail&cat=Todas">Todas</a>
            <a class="dropdown-item disabled" href="#" tabindex="-1" aria-disabled="true">disabled link</a>
          </div>
        </li>
        <li class="nav-item"><a class="nav-link" href="#">Ofertas</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Lo mas vendido</a></li>
        <li class="nav-item"><a class="nav-link" href="?action=contact">Contactanos</a></li>
      </ul>
    </nav>
    <nav class="navbar">
      <form class="form-inline" action="" method="get">
        <input id="search-input" class="form-control mr-sm-2" name="search" type="search" placeholder="Buscar" aria-label="Search">
        <button type="submit" id="search-button" class="btn btn-outline-success my-2 my-sm-0">Buscar</button>
      </form>
    </nav>
  </section>
</header>