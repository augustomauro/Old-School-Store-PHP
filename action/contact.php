<div class="container" id="contactUs">

  <?php require 'require/header.php'; ?>

  <div class="content-wrap">

    <div class="form-contactUs">
      <form action="?action=home" class="formulario" method="post">

        <div class="form-group">
          <label for="inputEmail">Email :</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="Email" required autofocus>
        </div>
        <div class="form-group">
          <label for="inputName">Nombre Completo :</label>
          <input type="text" name="fullname" id="fullname" class="form-control" placeholder="Nombre Completo" required>
        </div>
        <div class="form-group">
          <label for="inputOrder">Orden Nº :</label>
          <input type="text" name="order" id="order" class="form-control" placeholder="Orden Nº" required>
        </div>
        <div class="form-group">
          <label for="inputReason">Razón del Contacto ?</label>
          <select class="form-control" name="reason" id="reason" required>
            <option value="orden">Mi Orden</option>
            <option value="postOrden">Post Orden</option>
            <option value="pago">Pagos</option>
            <option value="devolver">Devolver o Cancelar Orden</option>
            <option value="daniado">Producto Dañado</option>
          </select>
        </div>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">Tu Mensaje</span>
          </div>
          <textarea name="textarea" id="textarea" class="form-control" aria-label="With textarea"></textarea>
        </div>
        <div class="contenedor-login">
          <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Enviar</button>
        </div>
      </form>
    </div>
  </div>

  <?php require 'require/footer.php'; ?>

</div>