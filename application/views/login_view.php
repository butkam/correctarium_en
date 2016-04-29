<form id="login_form" role="form" data-toggle="validator" name="login" accept-charset="utf-8" action="/login/" method="POST" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-12 text-center">
      <h1>Вход</h1>
      <div class="col-md-4 col-md-offset-4">
        <div class="form-group">
          <input id="login" type="email" class="form-control" name="email" placeholder="Эл. почта" required>
        </div>
      </div>
      <div class="col-md-4 col-md-offset-4">
        <div class="form-group">
          <input id="password" type="password" class="form-control" name="password" placeholder="Пароль" required>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-md-offset-4 text-center">
    <button type="submit" id="login" name="submit" class="btn btn-sm btn-default custom-btn wide-btn">Войти</button>
  </div>
</form>
