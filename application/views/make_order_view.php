<div class="row wrapper">
<div class="col-md-4 col-md-push-8">
	<div class="order-box" onclick="setOrderPriceAndDate()">
		<h4>Выберите услугу:</h4>
  		<div id="radio1" class="radio">
  		  <label>
  		    <input type="radio" name="optionsRadios" id="optionsRadios2" value="proof">
  		    Корректура
  		  </label>
  		</div>
  		<div id="radio1" class="radio">
  		  <label>
  		    <input type="radio" name="optionsRadios" id="optionsRadios3" value="edit" checked>
  		    Литературное редактирование
  		  </label>
  		</div>
	</div>
	<div id="order-proof-descr">
		<h4>Корректура</h4>
		<p>Исправление ошибок орфографии, грамматики и&nbsp;пунктуации. Ваш текст не&nbsp;будет изменен, из&nbsp;него просто исчезнут все ошибки. Для более глубокой обработки текста и&nbsp;исправления ошибок стиля выбирайте услугу редактирования.</p>
	</div>
	<div id="order-edit-descr">
		<h4>Редактирование</h4>
		<p>Корректура + исправление всех стилистических и&nbsp;логических ошибок. Все мысли и&nbsp;факты будут сохранены, но&nbsp;по&nbsp;форме текст может быть значительно переработан для придания благозвучности и&nbsp;повышения его продающей способности.</p>
	</div>
	<div class="price-time">
		<h4 class="order-price subdescription">Стоимость и сроки</h4>
  		<div id="price-holder">
  			<p>Введите текст, чтобы увидеть цену и&nbsp;время.</p>
  		</div>
		<div id="order-col"></div>
		<div id="time-col">
			<span class="order-time subdescription" id="time-text"></span>
			<div class="time-box">
				<span id="time"></span>
				<span id="minutes"></span>
			</div>
		</div>
	</div>
</div>
<div class="col-md-8 col-md-pull-4">
	<form name="order" id="order" accept-charset="utf-8" onkeyup="setOrderPriceAndDate()">
		<div class="form-group">
			<textarea class="form-control input-lg custom-textarea" id="text" rows="20" placeholder="Введите текст, который необходимо откорректировать" required></textarea>
      <span class="form-label right" id="text-counter"></span>
      <span class="form-label left" id="en-alert"></span>
			<div class="clearfix"></div>
		</div>
		<div class="form-group">
			<input type="text" id="comment" class="form-control input-lg comment" name="comment" placeholder="Добавьте короткий комментарий или ссылку" maxlength="300">
		</div>
		<div id="long-text-error"></div>
		<div id="emailInput" class="form-group">
			<input type="email" id="email" class="form-control input-lg custom-input" name="email" placeholder="Эл. почта" required>
			<span id="error" aria-hidden="true"></span>
			<div id="custom-error"></div>
		</div>
		<div class="form-group">
			<input type="text" id="name" class="form-control input-lg custom-input" name="name" placeholder="Имя">
		</div>
		<button id="order-btn" class="btn btn-lg btn-default custom-btn wide-btn" data-toggle="modal" onclick="return validate(event)">Заказать</button>
	</form>
	<input id="order_id" type="hidden" name="order_id" value="<?php print $data ?>"/>
</div>

<!-- Start Modal Window -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
	  	<button type="submit" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Оплата заказа</h4>
      </div>
		<form name="payment" method="post" action="https://www.liqpay.com/api/checkout" accept-charset="utf-8" onkeyup="validate()">
      	<div class="modal-body">
      	<div class="form-group">
			<input id="data" type="hidden" name="data"/>
			<input id="signature" type="hidden" name="signature"/>
      	</div>
		<div class="t-payment-details">
			<div class="t-service">
				<table class="table table-striped t-table-payment">
					<tr>
						<td>Услуга:</td>
						<td><strong><span id="order-type"></span></strong></td>
					</tr>
					<tr>
						<td>Стоимость:</td>
						<td><strong><span id="check-price">0,00</span> грн</strong></td>
					</tr>
				</table>
			</div>
			<div class="t-line"></div>
			<p class="t-payment-description">
				Пожалуйста, оплатите заказ. После этого заказ будет отправлен редактору. Как только текст будет готов, вы&nbsp;получите уведомление по эл. почте.
			</p>
		</div>
      	</div>
      	<div class="modal-footer">
        <button type="submit" type="button" id="buy-btn" class="btn btn-success" onclick="ga('send', 'event', 'Оплатить');" >Оплатить</button>
        </form>
      </div>
    </div>
</div>

<!-- End Modal Window -->

</div>
</div>
