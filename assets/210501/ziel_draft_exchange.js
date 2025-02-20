//Load HTTP module
const fs = require('fs');
const path = require('path');
const http = require("http");
const hostname = '127.0.0.1';
const port = 3001;
var ethers = require('ethers');
var ziel = require('./ziel');


const server = http.createServer(async (req, res) => {
  //Set the response HTTP header with HTTP status and Content type
  res.statusCode = 200;
  res.setHeader('Content-Type', 'application/json');
  
	console.log(req.url);
	console.log(req.method);
	
  if (req.url === '/exchange_submit') {
		let aRequest;
		req.on('data', async chunk => {
			aRequest = JSON.parse(chunk);
			console.log(aRequest);
	
if (aRequest.token_from.length !== 0)
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
				let oContract = new ethers.Contract(aRequest.token_from, abi, walletSigner);
				let balance = await oContract.balanceOf(aRequest.address_from);
				let symbol = await oContract.symbol();
				let name = await oContract.name();

				console.log(symbol);
				console.log(name);

				let iTokens = ethers.utils.parseUnits(aRequest.amount_from, 18);
				console.log(`iTokens: ${iTokens}`);

				oContract.transfer(aRequest.token_to, iTokens).then((tx) => {
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

}
else 
{
	
	console.log('aRequest - withoud contract');
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

/*
amount_from: '100',
address_from: '0xA9b1fd53086E67BA34F4945353b564E63a7FeBb0',
mnemonic: '',
token_from: '',
network: 'ropsten',
privatekey: '33ae9c6f8342a9516f0fdc5b34fb0bc7fb25108289eec38e08e902b8c388ed4e',
amount_to: '1',
address_to: '0xA9b1fd53086E67BA34F4945353b564E63a7FeBb0',
token_to: '0xB8c77482e45F1F44dE1745F52C74426C631bDD52'
*/
	
				let chainid = ethers.providers.getNetwork(aRequest.network);
				let provider = new ethers.providers.InfuraProvider(aRequest.network, ziel.sZielInfura);
				let walletSigner = wallet.connect(provider)
				let gas_limit = parseInt(aRequest.gas_limit); //5000000 //"0x100000" // Gas Limit & Usage by Txn: 5,000,000 | 21,000 (0.42%) 
				let gas_price = parseInt(aRequest.gas_price); //100000000000 //22500000000 // fast-33 average-30 slow-22.5 // 0.0000001 Ether (100 Gwei) // gas calculator

				const tx = {
					from: aRequest.address_from,
					to: aRequest.token_to,
					value: ethers.utils.parseEther(aRequest.amount_from),
					nonce: await provider.getTransactionCount(
						aRequest.address_from,
						"latest"
					),
					gasLimit: ethers.utils.hexlify(gas_limit),
					gasPrice: gas_price
				}

				try {
console.log(tx);

					walletSigner.sendTransaction(tx)
					.then(async function(tx){

						let balance = await provider.getBalance(aRequest.address_from);
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


