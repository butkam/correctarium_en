<div class="row">
  <div class="col-md-12">
    <div class="editors-form">
    <h1>Распределение заказов</h1>
      <form id="editor_form" role="form" data-toggle="validator" name="send_order" accept-charset="utf-8" action="/editor" method="POST" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <textarea id="order_intro" name="order_intro" rows="9" cols="40" class="form-control" placeholder="Введите вступление к рассылке" required>Это массовая рассылка для всех редакторов — партнеров Correctarium. Нам поступил новый срочный заказ. Подробности ниже. Пожалуйста, сообщите, сможете ли вы за него взяться. Мы подтвердим заказ первому ответившему редактору.&#13;&#10;&#13;&#10;Не приступайте к правке без подтверждения с нашей стороны.</textarea>
            </div>
            <div class="form-group" onchange="getMailGroup()">
              <select id="group_select" class="form-control">
                <option>Выбеите список рассылки</option>
                <?php

                foreach ($data['groups'] as $value) {
                  print $value;
                }

                ?>
              </select>
            </div>
            <div class="form-group">
              <textarea id="editors_group" name="editors_group" rows="6" cols="40" class="form-control" placeholder="Список рссылки" required></textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <textarea id="editor_comment" name="editor_comment" rows="9" cols="40" class="form-control" placeholder="Инструкции к заказу" required>Стандартные инструкции «Корректариума»</textarea>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <input id="client_name" type="text" class="form-control" name="client_name" placeholder="Имя заказчика" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <input id="deadline_date_time" type="datetime" class="form-control datepicker" name="deadline_date_time" placeholder="Дата и время" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <select id="order_type" name="order_type" class="form-control" oninput="setEditorPrice()">
                <option value="proof" selected>Корректура</option>
                <option value="edit">Литературное редактирование</option>
                <option value="en">Английский (редактирование)</option>
                <option value="tr">Перевод (русский ←→ украинский)</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group" oninput="setEditorPrice()">
              <input id="order_volume" type="number" class="form-control" name="volume" placeholder="Символов с пробелами" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon">грн</div>
                <input id="order_price" type="text" class="form-control" name="price" placeholder="Цена" required>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <input id="client_email" type="email" class="form-control" name="client_email" placeholder="Имейл заказчика" required>
            </div>
          </div>
        </div>
        <div id="order_file" class="form-group">
          <label for="exampleInputFile">Файл с текстом заказа</label>
          <input type="file" name="file" required>
          <p class="help-block">Загрузите файл в форматах (rtf, doc, docx, pdf, ppt, pptx, xls, xlsx)</p>
        </div>
        <button type="submit" id="order-btn" class="btn btn-sm btn-default custom-btn wide-btn" onclick="return validateEditorForm()">Отправить заказ</button>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <h2>Заказы</h2>
    <table class="table table-hover table-price">
      <tbody>
        <tr>
          <th>Номер</th>
          <th>Имя</th>
          <th>Услуга</th>
          <th>Цена</th>
          <th>Объем</th>
          <th>Срок сдачи</th>
        </tr>
      </tbody>
      <tbody>
        <?php

        foreach ($data['orders'] as $value) {
          echo $value;
        }

         ?>
      </tbody>
    </table>
  </div>
</div>
