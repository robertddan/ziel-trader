class Ziel_wallet {
  constructor() {
    // Dom manipulation
    this.wallet_dom();
		this.set_cookie_dom();
		//this.set_cookie("wallet", "", 365);
		
  }
	wallet_library() {

	}
  wallet_new(event) {
    // gather dom data
    // make request
    // set dom changes
    event.preventDefault();
    $('.wallet-spinner').show().siblings().hide();
		let aFormData = $('.wallet-new-step-2 form').serializeArray();		
		let aFormValues = {};
		for (var i = 0; i < aFormData.length; i++) aFormValues[aFormData[i]['name']] = aFormData[i]['value'];

    $.ajax({
      url: "api/wallet_new",
      method: 'post',
      data: {wallet : JSON.stringify(aFormValues)},
			context: this,
			async: true
    }).done(function(res) {
			//$('.nav-wallet-alerts .badge-counter').text(res.badges_counter);
			this.set_cookie("wallet", JSON.stringify(res), 365);
			this.set_cookie_dom();
			$('.wallet-account .info h6').text(res.address);
			$('.wallet-account .info h3').text('eth:'+ res.balance);
			$('.wallet-account').show().siblings().hide();
			window.location.hash = 'wallet-account';
    });
    return true;
  }
  wallet_import(evet) {
    // gather dom data
    // make request
    // set dom changes
    event.preventDefault();
    $('.wallet-spinner').show().siblings().hide();
		let aFormData = $('.wallet-import-step-2 form').serializeArray();
		let aFormValues = {};
		for (var i = 0; i < aFormData.length; i++) aFormValues[aFormData[i]['name']] = aFormData[i]['value'];

    $.ajax({
      url: "api/wallet_import",
      method: 'post',
			data: {wallet : JSON.stringify(aFormValues)},
			context: this,
			async: true
    }).done(function(res) {
			this.set_cookie("wallet", JSON.stringify(res), 365);
			this.set_cookie_dom();
			$('.wallet-account').show().siblings().hide();
			window.location.hash = 'wallet-account';
			
    });
    return true;
  }
  wallet_account() {

  }
  wallet_account_new() {

  }
  wallet_account_export() {

  }
  wallet_account_import() {

  }
  wallet_import_token() {

  }
  wallet_list_coins(event) {
    // gather dom data
    // make request
    // set dom changes
    event.preventDefault();
    //$('.wallet-spinner').show().siblings().hide();

		let aFormData = $('.wallet-account form').serializeArray();
		let aFormValues = {};
		for (var i = 0; i < aFormData.length; i++) aFormValues[aFormData[i]['name']] = aFormData[i]['value'];

    $.ajax({
      url: "api/wallet_list_coins",
      method: 'post',
			data: {wallet : JSON.stringify(aFormValues)},
			context: this,
			async: true
    }).done(function(res) {
			console.log(res);
			$('.wallet-abi').text(res.name +': '+ res.balance +' '+ res.symbol);
    });
    return true;
  }
  wallet_send() {
    // gather dom data
    // make request
    // set dom changes
    event.preventDefault();
    $('.wallet-spinner').show().siblings().hide();
		let aFormData = $('.wallet-send form').serializeArray();
		let aFormValues = {};
		for (var i = 0; i < aFormData.length; i++) aFormValues[aFormData[i]['name']] = aFormData[i]['value'];
console.log(aFormValues);
    $.ajax({
      url: "api/wallet_send",
      method: 'post',
			data: {wallet : JSON.stringify(aFormValues)},
			context: this,
			async: true
    }).done(function(res) {
			this.set_cookie("wallet", JSON.stringify(res), 365);
			this.set_cookie_dom();
			$('.wallet-account').show().siblings().hide();
			window.location.hash = 'wallet-account';
			$('.wallet-abi').text(res.tx_hash);
    });
    return true;
  }
  wallet_send_tokens() {
    // gather dom data
    // make request
    // set dom changes
    event.preventDefault();
    $('.wallet-spinner').show().siblings().hide();
		let aFormData = $('.wallet-send form').serializeArray();
		let aFormValues = {};
		for (var i = 0; i < aFormData.length; i++) aFormValues[aFormData[i]['name']] = aFormData[i]['value'];
console.log(aFormValues);
    $.ajax({
      url: "api/wallet_send",
      method: 'post',
			data: {wallet : JSON.stringify(aFormValues)},
			context: this,
			async: true
    }).done(function(res) {
			this.set_cookie("wallet", JSON.stringify(res), 365);
			this.set_cookie_dom();
			$('.wallet-account').show().siblings().hide();
			window.location.hash = 'wallet-account';
			$('.wallet-abi').text(res.tx_hash);
    });
    return true;
  }
  wallet_receive() {

  }
  wallet_swap() {

  }
  wallet_dom() {
    // panel
    $('.wallet-panel').show().siblings().hide();
    $('.wallet-import-step-1 button').click(function(){
      $('.wallet-import-step-2').show().siblings().hide();
      window.location.hash = 'wallet-import';
      return false;
    });
    $('.wallet-new-step-1 button').click(function(){
      $('.wallet-new-step-2').show().siblings().hide();
      window.location.hash = 'wallet-new';
      return false;
    });
    //back
    $('.wallet-import-step-2 .text-end').click(function(){
      $('.wallet-panel').show().siblings().hide();
      window.location.hash = 'wallet-panel';
      return false;
    });
    $('.wallet-new-step-2 .text-end').click(function(){
      $('.wallet-panel').show().siblings().hide();
      window.location.hash = 'wallet-panel';
      return false;
    });
    // account
    $('button.account-send').click(function(){
      $('.wallet-send').show().siblings().hide();
      window.location.hash = 'account-send';
      return false;
    });
    $('button.account-receive').click(function(){
      $('.wallet-receive').show().siblings().hide();
      window.location.hash = 'account-receive';
      return false;
    });
    $('button.account-token').click(function(){
      $('.wallet-import-token').show().siblings().hide();
      window.location.hash = 'account-token';
      return false;
    });
    $('button.account-new').click(function(){
      $('.wallet-account-new').show().siblings().hide();
      window.location.hash = 'account-new';
      return false;
    });
    $('button.account-export').click(function(){
      $('.wallet-account-export').show().siblings().hide();
      window.location.hash = 'account-export';
      return false;
    });
    $('button.account-import').click(function(){
      $('.wallet-account-import').show().siblings().hide();
      window.location.hash = 'account-import';
      return false;
    });
    $('button.account-transactions').click((e) => this.wallet_list_coins(e));
			//function(){
      //$('.wallet-account-transactions').show().siblings().hide();
      //window.location.hash = 'account-tx';
      //return false;
    //});
    // account panels back
    $('.wallet-send .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
    $('.wallet-receive .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
    $('.wallet-import-token .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
    $('.wallet-account-new .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
    $('.wallet-account-export .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
    $('.wallet-account-import .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
		$('.wallet-account-transactions .text-end').click(function(){
      $('.wallet-account').show().siblings().hide();
      window.location.hash = 'wallet-account';
      return false;
    });
		
    // forms
    $('.wallet-new-step-2 form').submit((e) => this.wallet_new(e));
    $('.wallet-import-step-2 form').submit((e) => this.wallet_import(e));
    $('.wallet-send form').submit((e) => this.wallet_send(e));
    $('.wallet-import-token form').submit((e) => this.wallet_import_token(e));
    $('.wallet-account-new form').submit((e) => this.wallet_account_new(e));
    $('.wallet-account-export form').submit((e) => this.wallet_account_export(e));
    $('.wallet-account-import form').submit((e) => this.wallet_account_import(e));
    // on empty hash
    if (window.location.hash.length == 0) {
      //window.location.hash = 'wallet-account';
      //$('.wallet-account').show().siblings().hide();
    }
  }
	
	set_cookie_dom() {
		let sWallet = this.get_cookie("wallet");
		if (sWallet.length == 0) return false;
		let res = JSON.parse(sWallet);
		if (res.address == undefined) return false;	
		if (res.address.length == 0) return false;	
		

		//window.location.hash = 'wallet-account';
		$('.wallet-account').show().siblings().hide();
		$('.wallet-account .info h6').text(res.address);
		$('.wallet-account .info h3').text(res.symbol +': '+ res.balance);
		// send panel
		$('#wallet-network').val(res.network);
		$('#wallet-privatekey').val(res.privatekey);
		$('.wallet-send #wallet-network').val(res.network);
		$('.wallet-send #wallet-privatekey').val(res.privatekey);
		$('.wallet-send #wallet-mnemonic').val(res.mnemonic);
		
		$('.wallet-send h3').val('Send ' +res.symbol);

		if (res.mnemonic !== null) $('#wallet-mnemonic').val(res.mnemonic);
		if (res.privatekey.length !== 0) $('#wallet-privatekey').val(res.privatekey);
		$('#wallet-send-from').val(res.address);
		// receive panel
		$('.wallet-receive span').text(res.address);
		// export panel
		$('.wallet-account-export #privatekey').text(res.privatekey);
		$('.wallet-account-export #mnemonic').text(res.mnemonic);
		console.log(res);

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

new Ziel_wallet();
