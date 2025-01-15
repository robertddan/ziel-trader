//Load HTTP module
const fs = require('fs');
const path = require('path');
const http = require("http");
const hostname = '127.0.0.1';
const port = 3000;

var ethers = require('ethers');
var ziel = require('./ziel');

/*
let networks = 'ropsten';
let address = '0xf3337E1F454b1DF8ABd68517Fd8C3B13E7aa0B90';

	var network = (networks == 'homestead'? '': '-'+ networks);
	var host = 'https://api'+ network +'.etherscan.io';
	var url = '/api?module=contract&action=getabi&address='
		+ address +'&apikey='+ ziel.sZielEthScan;

console.log(host + url);

async function get_abi(address, networks) {1

	console.log(host + url);
	//const response = await axios.get(host + url);
	//console.log(response);
	//return response.data.result
}

console.log(get_abi('0xf3337E1F454b1DF8ABd68517Fd8C3B13E7aa0B90', 'ropsten'));
*/


//Create HTTP server and listen on port 3000 for requests
const server = http.createServer(async (req, res) => {
  //Set the response HTTP header with HTTP status and Content type
  res.statusCode = 200;
  res.setHeader('Content-Type', 'application/json');
  
	console.log(req.url);
	console.log(req.method);
	
  if (req.url === '/wallet_new') {
		let aRequest;
		req.on('data', async chunk => {
			aRequest = JSON.parse(chunk);
			let provider = new ethers.providers.InfuraProvider(aRequest.network, ziel.sZielInfura);
			let wallet = ethers.Wallet.createRandom();
			let network = ethers.getDefaultProvider();
			let balance = await provider.getBalance(wallet.address);

			res.data = {address: wallet.address,
									mnemonic: wallet.mnemonic.phrase, 
									privatekey: wallet.privateKey, 
									balance: ethers.utils.formatEther(balance),
									network: aRequest.network};
			res.end(JSON.stringify(res.data));
		});
  } 
	else if (req.url === '/wallet_import') {
		let aRequest;
		await req.on('data', async chunk => {
			aRequest = JSON.parse(chunk);
			let wallet;
			let privatekey;
			let mnemonic;
			if (aRequest.mnemonic.length !== 0) {
				console.log('mm');
				wallet = ethers.Wallet.fromMnemonic(aRequest.mnemonic);
				mnemonic = aRequest.mnemonic;
				privatekey = wallet.privateKey,
				console.log(privatekey);
			} else if (aRequest.privatekey.length !== 0) {
				console.log('pp');
				wallet = new ethers.Wallet(aRequest.privatekey);
				mnemonic = wallet.mnemonic,
				privatekey = aRequest.privatekey,
				console.log(mnemonic);
			} else {
				res.end(JSON.stringify([]));	
			}

			let provider = new ethers.providers.InfuraProvider(aRequest.network, ziel.sZielInfura);
			let balance = await provider.getBalance(wallet.address);

			res.data = {address: wallet.address,
									mnemonic: mnemonic, 
									privatekey: privatekey, 
									balance: ethers.utils.formatEther(balance),
								 	network: aRequest.network};
			res.end(JSON.stringify(res.data));
		});
	}
	else if (req.url === '/wallet_send') {
		let aRequest;
		await req.on('data', async chunk => {
			aRequest = JSON.parse(chunk);

console.log(aRequest);
			
			if (aRequest.contract.length !== 0)
			{
				let provider = new ethers.providers.InfuraProvider(aRequest.network, ziel.sZielInfura);
				let wallet;
				let privatekey;
				let mnemonic;
				if (aRequest.mnemonic.length !== 0) {
					console.log('mm');
					wallet = ethers.Wallet.fromMnemonic(aRequest.mnemonic);
					mnemonic = aRequest.mnemonic;
					privatekey = wallet.privateKey,
					console.log(privatekey);
				} else if (aRequest.privatekey.length !== 0) {
					console.log('pp');
					wallet = new ethers.Wallet(aRequest.privatekey);
					mnemonic = wallet.mnemonic,
					privatekey = aRequest.privatekey,
					console.log(mnemonic);
				} else {
					res.end(JSON.stringify([]));	
				}

				let json_file = path.resolve(__dirname, "abi.json");
				let abi = JSON.parse(fs.readFileSync(json_file));
				let walletSigner = wallet.connect(provider);
				let oContract = new ethers.Contract(aRequest.contract, abi, walletSigner);
				let balance = await oContract.balanceOf(aRequest.from);
				let symbol = await oContract.symbol();
				let name = await oContract.name();

				console.log(symbol);
				console.log(name);

				let iTokens = ethers.utils.parseUnits(aRequest.amount, 18);
				console.log(`iTokens: ${iTokens}`);

				oContract.transfer(aRequest.to, iTokens).then((tx) => {
					res.data = {address: wallet.address,
										mnemonic: aRequest.mnemonic, 
										privatekey: aRequest.privatekey, 
										network: aRequest.network,
										balance: ethers.utils.formatEther(balance),
										name: name,
										symbol: symbol,
										tx_hash: tx.hash
									 };
					console.log(res.data);
					res.end(JSON.stringify(res.data));
				});

/*
				let oContract = new ethers.Contract(aRequest.contract, abi, provider);
				let balance = await oContract.balanceOf(aRequest.account);
				let symbol = await oContract.symbol();
				let name = await oContract.name();

				console.log(await oContract.name());
				console.log(await oContract.symbol());
				console.log(ethers.utils.formatEther(balance, 6));

				res.data = {address: wallet.address,
										mnemonic: aRequest.mnemonic, 
										privatekey: aRequest.privatekey, 
										network: aRequest.network,
										balance: ethers.utils.formatEther(balance),
										name: name,
										symbol: symbol};
				res.end(JSON.stringify(res.data));
*/

			}
			else
			{
				let wallet;
				if (aRequest.mnemonic.length !== 0) {
					console.log('mm');
					wallet = ethers.Wallet.fromMnemonic(aRequest.mnemonic);	
				} else if (aRequest.privatekey.length !== 0) {
					console.log('pp');
					wallet = new ethers.Wallet(aRequest.privatekey);
				} else {
					return res.end(JSON.stringify([]));	
				}

				let chainid = ethers.providers.getNetwork(aRequest.network);
				let provider = new ethers.providers.InfuraProvider(aRequest.network, ziel.sZielInfura);
				let walletSigner = wallet.connect(provider)
				let gas_limit = parseInt(aRequest.gas_limit); //5000000 //"0x100000" // Gas Limit & Usage by Txn: 5,000,000 | 21,000 (0.42%) 
				let gas_price = parseInt(aRequest.gas_price); //100000000000 //22500000000 // fast-33 average-30 slow-22.5 // 0.0000001 Ether (100 Gwei) // gas calculator

				const tx = {
					from: aRequest.from,
					to: aRequest.to,
					value: ethers.utils.parseEther(aRequest.amount),
					nonce: provider.getTransactionCount(
						aRequest.from,
						"latest"
					),
					gasLimit: ethers.utils.hexlify(gas_limit),
					gasPrice: gas_price
				}
				
				try {

					walletSigner.sendTransaction(tx)
					.then(async function(tx){
						let balance = await provider.getBalance(wallet.address);
						res.data = {address: wallet.address,
										mnemonic: aRequest.mnemonic, 
										privatekey: aRequest.privatekey, 
										network: aRequest.network,
										balance: ethers.utils.formatEther(balance),
										tx_hash: tx.hash};
						console.log(res.data);
						res.end(JSON.stringify(res.data));
					})
					.catch(async function(error){
						console.log('cathed js');
						console.log(error)
					});
				} catch (error) {
					console.log('cathed try');
					console.log(error)
					res.statusCode = 404;
					res.end();
				}

				
			}
		});
	}
	else if (req.url === '/wallet_list_coins') {
		let aRequest;
		await req.on('data', async chunk => {
			aRequest = JSON.parse(chunk);
console.log(aRequest);

			let wallet;
			if (aRequest.mnemonic.length !== 0) {
				console.log('mm');
				wallet = ethers.Wallet.fromMnemonic(aRequest.mnemonic);	
			} else if (aRequest.privatekey.length !== 0) {
				console.log('pp');
				wallet = new ethers.Wallet(aRequest.privatekey);
			} else {
				res.end(JSON.stringify([]));	
			}

			let json_file = path.resolve(__dirname, "abi.json");
			let abi = JSON.parse(fs.readFileSync(json_file));
			let provider = new ethers.providers.InfuraProvider(aRequest.network, ziel.sZielInfura);
			let oContract = new ethers.Contract(aRequest.contract, abi, provider);
			//let decimals = await oContract.decimals();
			let balance = await oContract.balanceOf(aRequest.account);
			let symbol = await oContract.symbol();
			let name = await oContract.name();
			
			console.log(ethers.utils.formatEther(balance));
			console.log(await oContract.name());
			console.log(await oContract.symbol());
			//console.log(await oContract.decimals());
			
			res.data = {address: wallet.address,
									mnemonic: aRequest.mnemonic, 
									privatekey: aRequest.privatekey, 
									network: aRequest.network,
									balance: ethers.utils.formatEther(balance),
									//decimals: decimals,
									name: name,
									symbol: symbol};
			res.end(JSON.stringify(res.data));
		});
	}
	else {
    res.statusCode = 404;
    res.end();
  }
});

//listen for request on port 3000, and as a callback function have the port listened on logged
server.listen(port, hostname, () => {
  console.log(`Server running at http://${hostname}:${port}/`);
});

/*
Safety
from the same IP - every minute
for the machine - the same

TODO
- send in another network
*/


/*
Balances

You could do the following:

const ethers = require('ethers');
const genericErc20Abi = require(..../.../Erc20.json);
const tokenContractAddress = '0x...';
const provider = ...; (use ethers.providers.InfuraProvider for a Node app or ethers.providers.Web3Provider(window.ethereum/window.web3) for a React app)
const contract = new ethers.Contract(tokenContractAddress, genericErc20Abi, provider);
const balance = (await contract.balanceOf((await provider.getSigners())[0].address)).toString();

https://ethereum.stackexchange.com/questions/101858/how-to-get-the-erc-20-token-balance-of-an-account-using-etherjs
*/


/*
Kovan:
carpet steak fold actual copper circle legal human change trust syrup hunt
ready fossil draft client anchor bar priority taxi height practice job erupt
{"address":"0x34a9D3119FeDE4721D1aA45A7319A52E46Cb8FF0","mnemonic":"hunt round replace street dove smoke silly sing wear wait stomach slight","privateKey":"0x0753656c07daa00f626395fc79c382868a419e721a0f6b65a955e831ec043051"}
0xB404c51BBC10dcBE948077F18a4B8E553D160084, 0xf41304c0f7636e426765172e1a37b922f6899f8e0ef3a49683a4a7a639acadd4, fat crouch friend degree that own pride mother bomb midnight sudden yellow
0x68f47a82faf4FBCAF8e876E96e5C278b131F7592, 0x802dc060accff67505b291af4c415597283f2e0ff04b29945cdb72f2b2be245a, army illegal bless genuine long eagle gospel naive side elegant glide opinion
*/