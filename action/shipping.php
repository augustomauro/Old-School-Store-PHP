<?php

if (! $_SESSION['usuario']) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}

// Verifica si el usuario pulsa boton "comprar", o si el carrito esta vacio antes de continuar
if (! isset($_POST['payment']) && ! isset($_POST['cart_pos_user'])) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
} elseif ($_SESSION['usuario'][0]['qty_cart'] == 0) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}

// Guarda la posicion del usuario en el carrito
$posUser = $_POST['cart_pos_user'];

?>

<div class="container" id="shipping">

    <?php require 'require/header.php'; ?>

    <div class="row">
        <div class="col">
            <h4>Confirmacion de Compra 2</h4>
            <!--<h6>Usuario ID (<?= $userId ?>) | Cantidad Items (<?= $posUser ? count($carts[$posUser]['products']) : 0 ?>)</h6>-->

            <output>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Dirección de envío <i class="fas fa-address-card"></i></h4>
                        <form action="?view=review-cart" method="post" role="form">
                            <div class="form-group">
                                <label for="address1">Direccion </label>
                                <input type="text" class="form-control" name="address1" placeholder="Calle Nº...">
                            </div> <!-- form-group.// -->

                            <!--<div class="form-group">
                                <label for="address2">Direccion 2</label>
                                <input type="text" class="form-control" name="address2" placeholder="entre calles...">
                            </div>  form-group.// -->

                            <div class="form-row">
                                <!--<div class="form-group col-md-6">
                                    <label for="phone1">Teléfono Contacto</label>
                                    <input type="tel" class="form-control" name="phone1" placeholder="+541176541230">
                                </div>  form-group.// -->

                                <!--<div class="form-group col-md-6">
                                    <label for="phone2">Teléfono Alternativo</label>
                                    <input type="tel" class="form-control" name="phone2" placeholder="+541176541230">
                                </div>  form-group.// -->

                                <div class="form-group col-md-6">
                                    <label for="city">Ciudad</label>
                                    <input type="text" class="form-control" name="city" placeholder="ciudad">
                                </div> <!-- form-group.// -->

                                <div class="form-group col-md-4">
                                    <label for="state">Estado</label>
                                    <input type="text" class="form-control" name="state" placeholder="estado">
                                </div> <!-- form-group.// -->

                                <div class="form-group col-md-2">
                                    <label for="zip">C.P.</label>
                                    <input type="text" class="form-control" name="zip" placeholder="cod. pos.">
                                </div> <!-- form-group.// -->
                            </div>

                            <label class="custom-control custom-checkbox">
				                <input type="checkbox" name="checkbox" class="custom-control-input" checked>
				                <div class="custom-control-label">Usar Datos de Mi Cuenta para el Envio
                                </div>
                            </label>
                            
                            <!--<label class="custom-control custom-checkbox">
                                <input type="checkbox" name="checkbox" class="custom-control-input">
				                <div class="custom-control-label">Retiro en persona
                                </div>
                            </label>-->


                            <input type="hidden" name="cart_pos_user" value="<?= $posUser ?>">
                            <button type="submit" name="shipping" class="subscribe btn btn-primary btn-block"> Confirmar </button>
                        </form>
                    </div> <!-- card-body.// -->
                </div> <!-- card .// -->
            </output>

        </div>
    </div>

    <?php require 'require/footer.php'; ?>

</div>