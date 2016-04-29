
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
<div class="container">
	<div class="row wrapper">
	<div class="col-md-12">
		<div class="t-card">
			<h1 class="t-payment">Оплата</h1>
			<div class="t-payment-details">
				<div class="t-service">
					<table class="table table-striped t-table-payment">
						<tr>
							<td>Услуга:</td>
							<td><strong><?= $data['orderLabel'] ?></strong></td>
						</tr>
						<tr>
							<td>Стоимость:</td>
							<td><strong><?= $data['price'] ?> грн</strong></td>
						</tr>
					</table>
				</div>
				<div class="t-line"></div>
				<p class="t-payment-description">
					Пожалуйста, оплатите заказ. После этого бот отправит текст редактору. Как только текст будет готов, вы&nbsp;получите уведомление в&nbsp;«Телеграме».
				</p>
			</div>
			<form name="payment" method="post" action="https://www.liqpay.com/api/checkout" accept-charset="utf-8">
				<button type="submit" class="btn btn-lg btn-success custom-btn t-payment-button">Оплатить с помощью Liqpay</button>
				<input id="data" type="hidden" name="data" value="<?= $data['liqpayData'] ?>"/>
				<input id="signature" type="hidden" name="signature" value="<?= $data['liqpaySignature'] ?>"/>
			</form>
		</div>
	</div>
	</div>
</div>
</body>
