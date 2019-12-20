<?php
// Lee la base de categorias principal que luego compara con la base en el HEADER
$categories = get_json('./json/categories.json');

$exist_cat = active_categories('QTY_CAT');
$qty_prod = active_categories('QTY_PROD');

?>

<div class="container" id="home">

  <?php require 'require/header.php'; ?>

  <div class="content-wrap">

    <section class="destacados">
      <h2><mark>Productos Destacados</mark></h2>
      <div class="bd-example">
        <div class="row justify-content-center">
          <div class="col-12 col-md-11 col-lg-8 ">
            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
              <ol class="carousel-indicators">
                <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
              </ol>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/ejemplo1.png" class="d-block w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <h5>SEGA Mega Drive</h5>
                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="img/ejemplo2.png" class="d-block w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Atari</h5>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="img/ejemplo3.png" class="d-block w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Camara Polaroid</h5>
                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
                  </div>
                </div>
              </div>
              <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
          </div>
    </section>

    <section class="stock">

      <h4> <mark> Nuestros Productos </mark> </h4>

      <?php foreach ($categories as $category) : ?>
      <article class="stock-productos">
        <h5><?= $category['name'] ?></h5>
        <a href="<?= in_array($category['name'], $exist_cat, true) ? '?view=detail&cat='.$category['name'] : '#' ?>"><img src="<?= $category['image'] ?>" alt=""></a>
        <div>
        <?php $qty = isset($qty_prod[$category['name']]) ? $qty_prod[$category['name']] : 0 ?>
        <small><?= $qty ?> Producto<?= ($qty > 1 || $qty == 0) ? 's' : '' ?></small>
        </div>
      </article>
      <?php endforeach; ?>

    </section>


    <section class="frecuentes">

      <h4> <mark>Preguntas frecuentes</mark> </h4>

      <article class="frecuentes-tips">
        <div class="">
          <img src="img/tarjetas.png" alt="">
        </div>
        <h6>¿Como pago el producto?</h6>
        <p>Los productos los podes pagar con tarjetas de débito y crédito. Hay varios descuentos y promociones según el
          banco que tengas.</p>
      </article>

      <article class="frecuentes-tips">
        <div class="">
          <img src="img/envios.png" alt="">
        </div>
        <h6>¿Como enviamos el producto?</h6>
        <p>Después de comprar tu pedido, nos contactamos con vos para coordinar la entrega del producto. ¡Si vivis en
          Capital la entrega es en el día!</p>
      </article>

      <article class="frecuentes-tips">
        <div class="">
          <img src="img/seguridad.png" alt="">
        </div>
        <h6>¿Es seguro el sitio?</h6>
        <p>Mas de 200.000 ventas no avalan. Somos un sitio seguro, que dispone de productos originales y con garantía.
        </p>
      </article>

    </section>


    <section class="contacto">

      <div class="mensaje-newsletter">
        <p> ¡Queremos seguir conectados! Ingresa tu e-mail y recibí semanalmente en tu correo las mejores ofertas retro
          que tenemos para vos. </p>
      </div>
      <div class="correo">
        <form action="" method="post">
          <div class="input-group flex-nowrap">
            <div class="input-group-prepend">
              <span class="input-group-text" id="addon-wrapping">@</span>
            </div>
            <input type="email" name="newsletter" id="newsletter" class="form-control" placeholder="Email"
              aria-label="Newsletter" aria-describedby="addon-wrapping">
            <button class="btn btn-outline-success" type="submit">Enviar</button>
          </div>
        </form>
      </div>

    </section>


    <section class="pie-pagina">

      <article>
        <h5>Servicios al cliente</h5>
        <ul>
          <li> <a href="#"> Centro de ayuda </a></li>
          <li> <a href="#"> Reembolso de dinero </a></li>
          <li> <a href="#"> Términos y Políticas </a></li>
          <li> <a href="#"> Disputa abierta </a></li>
        </ul>
      </article>

      <article>
        <h5>Sobre Nosotros</h5>
        <ul>
          <li> <a href="#"> Nuestra historia </a></li>
          <li> <a href="#"> Como comprar </a></li>
          <li> <a href="#"> Entregas y pagos </a></li>
          <li> <a href="#"> Ofertas semanales </a></li>
        </ul>
      </article>

      <article>
        <h5>Contactanos</h5>
        <ul>
          <li> <strong>Telefono:</strong> <a href="tel:+5412344321"> +54 1234-4321 </a></li>
          <li> <strong>Direccion:</strong> <a href="https://goo.gl/maps/5xoGKZzoD6cZ9G4e9" target="_blank"> Segurola y
              Habana, Buenos Aires. </a></li>
          <li> <strong>Email:</strong> <a href="mailto:info@oldschoolstore.com.ar?subject=Consulta">
              info@oldschoolstore.com.ar </a></li>
        </ul>
      </article>

    </section>
  </div>

  <?php require 'require/footer.php'; ?>

</div>