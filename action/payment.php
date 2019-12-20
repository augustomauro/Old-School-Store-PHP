<?php

if (! $_SESSION['usuario']) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}

// Verifica si el usuario pulsa boton "comprar", o si el carrito esta vacio antes de continuar
if (! isset($_POST['buy']) && ! isset($_POST['cart_pos_user'])) {
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

<div class="container" id="payment">

    <?php require 'require/header.php'; ?>

    <div class="row">
        <div class="col">
            <h4>Confirmacion de Compra 1</h4>
            <!--<h6>Usuario ID (<?= $userId ?>) | Cantidad Items (<?= $posUser ? count($carts[$posUser]['products']) : 0 ?>)</h6>-->

            <output>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Metodo de Pago <i class="fas fa-money-check-alt"></i></h4>
                        <form action="?action=shipping" method="post" role="form">
                            <div class="form-group">
                                <label for="username">Nombre en la Tarjeta</label>
                                <input type="text" class="form-control" name="username" placeholder="Ex. John Smith">
                            </div> <!-- form-group.// -->

                            <div class="form-group">
                                <label for="cardNumber">Numero de Tarjeta</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="cardNumber" placeholder="XXXX XXXX XXXX XXXX" maxlength="16">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fab fa-cc-visa"></i> &nbsp; <i class="fab fa-cc-amex"></i> &nbsp;
                                            <i class="fab fa-cc-mastercard"></i>
                                        </span>
                                    </div>
                                </div> <!-- input-group.// -->
                            </div> <!-- form-group.// -->

                            <div class="row">
                                <div class="col-md flex-grow-0" id="expiration">
                                    <div class="form-group">
                                        <label><span class="hidden-xs">Vencimiento</span> </label>
                                        <div class="form-inline" style="min-width: 250px">
                                            <select class="form-control" style="width:130px">
                                                <option>MM</option>
                                                <option>01 - Ene</option>
                                                <option>02 - Feb</option>
                                                <option>03 - Mar</option>
                                                <option>04 - Abr</option>
                                                <option>05 - May</option>
                                                <option>06 - Jun</option>
                                                <option>07 - Jul</option>
                                                <option>08 - Ago</option>
                                                <option>09 - Sep</option>
                                                <option>10 - Oct</option>
                                                <option>11 - Nov</option>
                                                <option>12 - Dic</option>
                                            </select>
                                            <span style="width:20px; text-align: center"> / </span>
                                            <select class="form-control" style="width:100px">
                                                <option>YY</option>
                                                <option>2020</option>
                                                <option>2021</option>
                                                <option>2022</option>
                                                <option>2023</option>
                                                <option>2024</option>
                                                <option>2025</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title=""
                                            data-original-title="3 digits code on back side of the card">CVV <i
                                                class="fa fa-question-circle"></i></label>
                                        <input class="form-control" type="text" min="000" max="999" maxlength="3" style="width: 100px">
                                    </div> <!-- form-group.// -->
                                </div>
                            </div> <!-- row.// -->

                            <p class="alert alert-success"> <i class="fa fa-lock"></i> Some secureity information Lorem
                                ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod</p>

                            <input type="hidden" name="cart_pos_user" value="<?= $posUser ?>">
                            <button type="submit" name="payment" class="subscribe btn btn-primary btn-block"> Confirmar </button>
                        </form>
                    </div> <!-- card-body.// -->
                </div> <!-- card .// -->
            </output>

        </div>
    </div>

    <?php require 'require/footer.php'; ?>

</div>