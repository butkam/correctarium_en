Parse.initialize(
	"VGKLml8ecUz28BVBmfsmvr3OyxjErVf7ATnuQWQL",
	"2aUEuEnxVtfmQgV1whVboC99MRtXJlDAvFNdYLLf"
);

function getMailGroup() {
	var groupId = { mail_group: getValue('group_select') };
	sendOrderData(groupId, "/editor/", function(response) {
		setInnerHtml("editors_group", response.group);
	});
}

$(function () {
    $('#deadline_date_time').datetimepicker({
			locale: 'ru',
			format: 'YYYY/MM/DD HH:mm'
		});
});

function setEditorPrice() {
	if (getValue('order_volume') !== "" && getValue('order_type') !== "") {
		var price = getOrderPrice(getValue('order_volume'), getValue('order_type'));
		setValue('order_price', price.split("."));
	} else {
		setValue('order_price', "");
	}
}

function getYear() {
	moment.locale('ru');
	var year = moment().year();

	return year;
}

setInnerHtml("year", getYear());
if (document.getElementById("pr100")) {
	setMinCharPrice();
	setPricesIntoTable();
}

// class="ccy">≈ $<span id="usd">0,00</span> / €<span id="eur">0,00</span></div>
if (document.getElementById("order-col")) {
	var priceBox = '<div class="price-box"><span id="price"></span> ₴</div><div';
	setInnerHtml("order-col", priceBox);
}

function getFormData() {
	var inputData = {
		email: getForms("order", "email"),
		text: getForms("order", "text"),
		name: getForms("order", "name")
		};

	return inputData;

}

function analyseText(text) {
	var latin=[];
	var cyrill=[];
	for (var i = 0; i <= text.length; i++) {
		if (/[А-я]/.test(text[i])) {
			cyrill.push(text[i]);
		} else if (/[A-z]/.test(text[i])) {
			latin.push(text[i]);
		}
	}
	var latinPrcentage = latin.length / (latin.length + cyrill.length) * 100;
	return latinPrcentage;
}

function getOrderType() {
	if (analyseText(getFormData().text) >= 50) {
		orderType = "en";
	} else {
		orderType = $('input[name="optionsRadios"]:checked').val();
	}
	return orderType;
}

function getMistakes() {
	var mistakes = document.getElementById('show-mistks').checked;

	switch(mistakes) {
	case true:
		setStyle("example-text-origin", "block");
		setStyle("example-text-mistakes", "none");
	    break;
	case false:
		setStyle("example-text-origin", "none");
		setStyle("example-text-mistakes", "block");
	    break;
	default:
	    setStyle("example-text-origin", "none");
	    setStyle("example-text-mistakes", "block");
	}
}

function getPaymentOptions(orderType) {
	switch(orderType) {
		case "edit":
			var option = {
				minimumChargeSym: 100,
				pricePerSym: 0.05,
				orderName: "Литературное редактирование",
				minTerm: 15,
				speed: 33,
			};
			break;
		case "proof":
			option = {
				minimumChargeSym: 100,
				pricePerSym: 0.025,
				orderName: "Корректура",
				minTerm: 15,
				speed: 66,
			};
			break;
		case "en":
			option = {
				minimumChargeSym: 100,
				pricePerSym: 0.1,
				orderName: "Редактирование (английский)",
				minTerm: 15,
				speed: 20,
			};
			break;
		default:
			option = {
				minimumChargeSym: 100,
				pricePerSym: 0.025,
				orderName: "Корректура",
				minTerm: 15,
				speed: 33,
			};
	}
	return option;
}

function getForms(formName, type) {
	return document.forms[formName][type].value;
}

function getValue(id) {
	return document.getElementById(id).value;
}
function setValue(id, value) {
	return document.getElementById(id).value = value;
}

function setInnerHtml(id, html) {
	return document.getElementById(id).innerHTML = html;
}

function getInnerHtml(id) {
	return document.getElementById(id).innerHTML;
}

function setStyle(id, style) {
	return document.getElementById(id).style.display = style;
}

function removeAttr(id, name, param) {
	return document.getElementById(id).removeAttribute(name, param);
}

function setAttr(id, name, param) {
	return document.getElementById(id).setAttribute(name, param);
}

function getOrderData() {
	var orderData = {
		email: getValue("email"),
		text: getValue("text").replace(/\n/g, "<br />"),
		price: getInnerHtml("price"),
		time: getInnerHtml("time"),
		order_id: getValue("order_id"),
		name: getValue("name"),
		comment: getValue("comment"),
		orderType: getOrderType(),
		humanOrderType: getPaymentOptions(getOrderType()).orderName,
	};

	return orderData;
}

function validate(event) {
	var patt  = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	var email = getFormData().email;
	var text  = getFormData().text;
	var customError   = "<span class=\"custom-error\">Пожалуйста, введите правильный \
                      адрес эл.&nbsp;почты</span><span class=\"triangle\"></span>";

	var longTextError = '<span class="custom-error long-text-error">К сожалению, \
                      отправить текст длиной более 10 000 символов через сайт нельзя. \
                      Пришлите его нам на почту: <a href="\
											mailto:no.more.spelling.errors@gmail.com?Subject=Новый заказ | \
											Correctarium">no.more.spelling.errors@gmail.com</a>.</span><span \
											class="triangle triangle-up"></span>';

	var preload = '<div class="over-all"></div><div class="preloader"><img src="\
                /gfx/preloader.gif"></div>';

	if (email.match(patt) && email && text !== "" && text.length <= 10000) {
		removeAttr("emailInput", "class", "form-group has-error");
		setAttr("emailInput", "class", "form-group");
		setAttr("order-btn", "data-target", "#myModal");
		event.preventDefault();
		sendOrderData(getOrderData(), '/make-order/');

		return true;

	} else {
		removeAttr("emailInput", "class", "form-group");
		removeAttr("order-btn", "data-target", "#myModal");
		setAttr("emailInput", "class", "form-group has-error");

		if (email.match(patt)) {
			setInnerHtml("custom-error", "");
		} else {
			setInnerHtml("custom-error", customError);
		}

		if (text.length <= 10000) {
			setInnerHtml("long-text-error", "");
		} else {
			setInnerHtml("long-text-error", longTextError);
		}

		return false;
	}

}

function getOrderPrice(volume, orderType) {
	if (volume > getPaymentOptions(orderType).minimumChargeSym) {
		var quote = volume * getPaymentOptions(orderType).pricePerSym;
	} else {
		quote = getPaymentOptions(orderType).minimumChargeSym *
		        getPaymentOptions(orderType).pricePerSym;
	}

	return quote.toFixed(2);
}

function setOrderPriceAndDate() {
	setInnerHtml(
		"price",
		getOrderPrice(getFormData().text.length,
		getOrderData().orderType).split(".")
	);
	setInnerHtml(
		"check-price",
		getOrderPrice(getFormData().text.length,
		getOrderData().orderType).split(".")
	);
	if (getOrderData().orderType == "en") {
		setInnerHtml("en-alert", "Английский язык");
		setAttr("radio1", "class", "radio disabled");
		setAttr("optionsRadios2", "disabled", "");
		document.getElementById("radio1").style.color = "#ACACAC";
	} else if (getOrderData().orderType != "en") {
		setInnerHtml("en-alert", "");
		setAttr("radio1", "class", "radio");
		removeAttr("optionsRadios2", "disabled", "");
		document.getElementById("radio1").style.color = "#333";
	}

	if (getFormData().text !== "" && getFormData().text !== null) {
		setStyle("order-col", "block");
		setStyle("time-col", "block");
		setStyle("price-holder", "none");
	} else {
		setStyle("order-col", "none");
		setStyle("time-col", "none");
		setStyle("price-holder", "block");
		setInnerHtml("en-alert", "");
		setInnerHtml("text-counter", "");
		setAttr("radio1", "class", "radio");
		removeAttr("optionsRadios2", "disabled", "");
		document.getElementById("radio1").style.color = "#333";
	}

	var date = moment();
	setInnerHtml("time", getWorkingHours(date));
	if (getOrderData().orderType == "edit") {
		setStyle("order-edit-descr", "block");
		setStyle("order-proof-descr", "none");
		setAttr("text", "placeholder", "Введите текст, который необходимо откорректировать");
	} else if (getOrderData().orderType == "proof") {
		setStyle("order-proof-descr", "block");
		setStyle("order-edit-descr", "none");
		setAttr("text", "placeholder", "Введите текст, который необходимо отредактировать");
	}
	setInnerHtml("order-type", getPaymentOptions(getOrderType()).orderName);
	setInnerHtml("text-counter", getOrderData().text.length);

	sendOrderData(getOrderData(), "/payment/", function(response) {
		setAttr("data", "value", response.data);
		setAttr("signature", "value", response.signature);
	});
}

/**
 * @TODO Дублирование кода из setOrderPriceAndDate() переписать
 */

if (getFormData().text === "" && getFormData().text === null) {
	setStyle("order-col", "block");
	setStyle("time-col", "block");
} else {
	setStyle("order-col", "none");
	setStyle("time-col", "none");
}

function getOrderTerm(volume, orderType) {
	if (volume <= getPaymentOptions(orderType).minimumChargeSym) {
		var term = getPaymentOptions(orderType).minTerm;
	} else {
		term = getPaymentOptions(orderType).minTerm +
					(volume / getPaymentOptions(orderType).speed);
	}

	return term;
}

function getWorkingHours(date) {
	var term = getOrderTerm(getFormData().text.length, getOrderType());
	if (date.hours() <= 10 && date.hours() >= 0) {
		startDate = date.hours(10) && date.minutes(0) && date.seconds(0);
		deadline = startDate.seconds(term * 60).format("D MMMM в H:mm");
	} else if (date.hours() == 23 && date.minutes() >= 0 && date.minutes() <= 59) {
		startMinutes = date.minutes();
		startDate = date.minutes(660 + startMinutes);
		deadline = startDate.seconds(term * 60).format("D MMMM в H:mm");
	} else {
		if (term >= 60) {
			var hours = term / 60;
			var minutes = term % 60;
			var humanTerm = Math.round(hours) + " " + units(Math.round(hours),
			{nom: 'час', gen: 'часа', plu: 'часов'}) + " " +
			Math.round(minutes) + " " + units(Math.round(minutes),
			{nom: 'минута', gen: 'минуты', plu: 'минут'}
		);
			deadline = humanTerm;
		} else {
			deadline = Math.round(term) + " " + units(Math.round(term),
			{nom: 'минута', gen: 'минуты', plu: 'минут'});
		}
	}
	return deadline;
}

/**
 * Возвращает единицу измерения с правильным окончанием
 *
 * @param {Number} num      Число
 * @param {Object} cases    Варианты слова {nom: 'час', gen: 'часа', plu: 'часов'}
 * @return {String}
 */
function units(num, cases) {
    num = Math.abs(num);

    var word = '';

    if (num.toString().indexOf('.') > -1) {
        word = cases.gen;
    } else {
        word = (
            num % 10 == 1 && num % 100 != 11
                ? cases.nom
                : num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20)
                    ? cases.gen
                    : cases.plu
        );
    }

    return word;
}
function sendOrderData(data, url, callback) {
	$.ajax({
	    type: "POST",
	    url: url,
	    data: data,
	    dataType:'JSON',
			success: callback
	});
}

function setCurrency(price) {
	$.getJSON('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5', function(data) {
	  var usd = price / data[2].buy;
	  var eur = price / data[0].buy;

	  setInnerHtml("usd", usd.toFixed(2).split("."));
	  setInnerHtml("eur", eur.toFixed(2).split("."));
	});
}

function setPricesIntoTable() {
	var tableIDs = [
		'pr100',
		'ed100',
		'en100',
		'pr500',
		'ed500',
		'en500',
		'pr1000',
		'ed1000',
		'en1000',
		'pr5000',
		'ed5000',
		'en5000',
		'pr10000',
		'ed10000',
		'en10000'
	];

 var volumes = [
	 getInnerHtml('volume1'),
	 getInnerHtml('volume2'),
	 getInnerHtml('volume3'),
	 getInnerHtml('volume4'),
	 getInnerHtml('volume5'),
 ];

 var prices = [
	 getPaymentOptions("proof").pricePerSym,
	 getPaymentOptions("edit").pricePerSym,
	 getPaymentOptions("en").pricePerSym
 ];
var costs = [];
 for (var volume in volumes) {
	 for (var price in prices) {
		 var cost = volumes[volume] * prices[price];
		 costs.push(cost);
	 }
 }
 for (var i = 0; i < tableIDs.length; i++) {
 	setInnerHtml(tableIDs[i], costs[i]);
 }
}

function setMinCharPrice() {
	var editPrice = getPaymentOptions("edit").minimumChargeSym *
                  getPaymentOptions("edit").pricePerSym + " ₴";
	var proofPrice = getPaymentOptions("proof").minimumChargeSym *
	                 getPaymentOptions("proof").pricePerSym + " ₴";
	setInnerHtml("edit-min-price", editPrice.split("."));
	setInnerHtml("proof-min-price", proofPrice.split("."));
}

window.onbeforeunload = closingCode;
function closingCode(){
   if (getFormData().text !== "" && getFormData().text !== null) {
   	ga('send', 'event', 'Текст', getFormData().text);
   }
   return null;
}
