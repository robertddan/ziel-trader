class Ziel_exchange {
  constructor() {
    // Dom manipulation
		this.exchange_dom();
		this.set_cookie_dom();
  }
	exchange_submit(event) {
    // gather dom data
    // make request
    // set dom changes
    event.preventDefault();
		let aFormData = $('.exchange-page form').serializeArray();
		let aFormValues = {};
		for (var i = 0; i < aFormData.length; i++) aFormValues[aFormData[i]['name']] = aFormData[i]['value'];
console.log(aFormValues);

    $.ajax({
      url: "api/exchange_submit",
      method: 'post',
			data: {exchange : JSON.stringify(aFormValues)},
			context: this,
			async: true
    }).done(function(res) {
			console.log(res);
			$('.client_result').text(res);
    });
    return true;
	}
	exchange_dom() {
    // forms
    $('.exchange-page form').submit((e) => this.exchange_submit(e));
	}
	set_cookie_dom() {
		let sExchange = this.get_cookie("wallet");
		if (sExchange.length == 0) return false;
		let res = JSON.parse(sExchange);

		if (res.address.length !== 0)
		{
			window.location.hash = 'exchange';
			$('#wallet-privatekey').val(res.privatekey);
			$('#wallet-mnemonic').val(res.mnemonic);
			console.log(res);
		}
	}	
	set_cookie(cname, cvalue, exdays) {
		const d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		let expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}
	get_cookie(cname) {
		let name = cname + "=";
		let ca = document.cookie.split(';');
		for(let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
	checkCookie() {
		let user = get_cookie("username");
		if (user != "") {
			alert("Welcome again " + user);
		} else {
			user = prompt("Please enter your name:", "");
			if (user != "" && user != null) {
				set_cookie("username", user, 365);
			}
		}
	}
}

new Ziel_exchange();
