/**
* @author justin (with e)
* @copyright 2018
*/

function getHashPassword(password){
	var salt = generateSalt(128);

	return GibberishAES.enc(password, salt);
}

function generateSalt(salt_limit=16){
	var salt = "";
	var character = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	for(var i = 1; i <= salt_limit; i++){
		var index = Math.floor((Math.random() * character.length) + 1);
		salt += character[index];
	}

	document.cookie = "salt="+ salt; // <<< save salt in cookie
	return salt;
}