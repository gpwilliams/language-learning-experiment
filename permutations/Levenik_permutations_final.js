// new permutations with levenshtein edit distance

function generate() {
	// intialise vowels and consonants sets
	var vowels = ['a', 'e', 'i', 'o', 'u'];
	var consonants = ['m', 'n', 's', 'k', 'b', 'd', 'f', 'l'];

	// initialise 2 consonant onset and coda restrictions
	var twoConsonantOnset = ['sm', 'sn', 'sk', 'sl', 'kl', 'fl', 'bl'];
	var twoConsonantCoda = ['fs', 'lm', 'ls', 'lf', 'ln', 'lk', 'ld', 'sk', 'ns', 'nd', 'bd'];

	// get construction
	var construction = document.getElementById('constructionInput').value;
	
	// generate base set of all words
	var set = anyConstruction(construction, vowels, consonants);

	// randomise the set
	if (document.getElementById('randomiseCheckBox').checked === true)
		set = randomiseArray(set);

	// filter out non-permissable consonant onsets and codas
	set = filterOutOnsetsAndCodasCC(set, consonants, vowels, twoConsonantOnset, twoConsonantCoda);

	console.log('NEW GENERATION COMPLETE:');

	// print complete set to console
	printConsole(set);
	console.log('');

	//print to HTML
	printHTML(set);
}

// filter array to exclude regular expression pattern and return a new filtered array
function filterOut(array, regExpr) {
	var filteredArray = [];
	var count = 0;
	for (var n = 0; n < array.length; n++) {
		if (regExpr.test(array[n].name) === false) {
			filteredArray.push({name: array[n].name, value: count});
			count++;
		}
	}
	return filteredArray;
}

// filter array to exclude everything but the regular expression pattern and return a new filtered array
function keepInOnly(array, regExpr) {
	var filteredArray = [];
	var count = 0;
	for (var n = 0; n < array.length; n++) {
		if (regExpr.test(array[n].name) === true) {
			filteredArray.push({name: array[n].name, value: count});
			count++;
		}
	}
	return filteredArray;
}

// filter out double consonants
// NOT NEEDED if double consonants are already excluded as onsets and codas
function filterOutDoubleConsonants(array, consonants) {
	var filteredArray = [];
	var regExprString = '';

	// double consonant regular expression string
	for (var g = 0; g < consonants.length - 1; g++) {
		regExprString += (consonants[g] + consonants[g] + '|');
	}
	regExprString += (consonants[consonants.length - 1] + consonants[consonants.length - 1]);

	filteredArray = filterOut(array, new RegExp(regExprString));

	return filteredArray;
}

// filter out two consonant onset and coda combinations that do not meet the given ones
function filterOutOnsetsAndCodasCC(array, consonants, vowels, onsets, codas) {
	console.log('FILTERING OUTPUT...');
	var filteredArray = [];
	var regExprStringConsonant = '(';
	var regExprStringVowels = '(';
	var regExprStringOnsets = '(';
	var regExprStringCodas = '(';
	var regExprStringCC = '';
	
	// --- Create Regular Expression Strings from Input
	
	// single consonant regular expression string
	for (var c = 0; c < consonants.length - 1; c++) {
		regExprStringConsonant += consonants[c] + '|';
	}
	regExprStringConsonant += consonants[consonants.length - 1] + ')';

	// single vowel regular expression string
	for (var v = 0; v < vowels.length - 1; v++) {
		regExprStringVowels += vowels[v] + '|';
	}
	regExprStringVowels += vowels[vowels.length - 1] + ')';

	// single onset regular expression string
	for (var o = 0; o < onsets.length - 1; o++) {
		regExprStringOnsets += onsets[o] + '|';
	}
	regExprStringOnsets += onsets[onsets.length - 1] + ')';
	
	// single coda regular expression string
	for (var d = 0; d < codas.length - 1; d++) {
		regExprStringCodas += codas[d] + '|';
	}
	regExprStringCodas += codas[codas.length - 1] + ')';

	// --- Process Array
	regExprStringCC = '((' + regExprStringConsonant + ')(' + regExprStringConsonant + '))';
	var count = 0;
	for (var i = 0; i < array.length; i++) {
		var ignored = false;
		// if word begins with 2 consonants	
		if (RegExp('^' + regExprStringCC).test(array[i].name)) {
			// ignore word if it does not meet onset rules in beginning
			if (!RegExp('^' + regExprStringOnsets).test(array[i].name)) {
				ignored = true;
			}
		}
		// if word has a consonant cluster inside of it (CC surrounded by ANY CHARACTERS)
		// TODO: THIS WILL NOT WORK FOR WORDS THAT HAVE AT LEAST ONE CORRECT INWORD ONSET BUT ALSO OTHER INCORRECT INWORD ONES
		if (RegExp('.+' + regExprStringCC + '.+').test(array[i].name)) {
			// ignore word if it does not meet onset rules
			if (!RegExp('.+' + regExprStringOnsets + '.+').test(array[i].name)) {
				ignored = true;
			}
		}
		// if word ends with 2 consonants
		if (RegExp(regExprStringCC + '$').test(array[i].name)) {
			// ignore word if it does not meet coda rules
			if (!RegExp(regExprStringCodas + '$').test(array[i].name)) {
				ignored = true;
			}
		}
		// if reached this point without ignoring the word yet then it must be good so push
		if (ignored === false) {
			filteredArray.push({name: array[i].name, value: count});
			count++;
		}
	}
	
	return filteredArray;
}

// print complete array values to console
function printConsole(array) {
	for (var i = 0; i < array.length; i++) {
		console.log(array[i]);
	}
}

// save array values to variable for output on HTML (GW)
function printHTML(array) {
	var wordsHTML = '';
	for (var k = 0; k < array.length; k++) {
		wordsHTML += array[k].name + '\n';
	}
	document.getElementById("wordsHTML").innerHTML = String(wordsHTML);
}

// randomise array
function randomiseArray(array) {
	console.log('RANDOMISING OUTPUT...');
	
	// generate random values for array
	for (var i = 0; i < array.length; i++) {
		array[i].value = Math.random();
	}
	
	//create a function to compare words by their random values
	function compareNumber(a, b) {
		return (a.value) - (b.value);
	}

	//sort array by their (compared) random values
	array.sort(compareNumber);

	// could return values back to proper order
	for (var i = 0; i < array.length; i++) {
		array[i].value = i;
	}
	
	//return words so that the CVC function can print outputs
	return array;
}

// any constructions function
function anyConstruction(construction, vowels, consonants) {
	console.log('GENERATING...');
	
	var words = [];

	// iterate over construction pattern
	for (var i = 0; i < construction.length; i++) {
		// add consonant or vowel characters accordingly
		if ((construction[i] === 'C') || (construction[i] === 'c')) {
			words = addCharacters(words, consonants);
		}
		else if ((construction[i] === 'V') || (construction[i] === 'v')) {
			words = addCharacters(words, vowels);
		}
		else {
			// error
			alert('Incorrect construction pattern detected!');
			break;
		}
	}

	return words;
}

// create a newWords array that is 
// all combinations of the previous words array and the characters from the given characters array 
function addCharacters(words, characters) {
	// create a new empty array
	var newWords = [];
	
	// if the given words array is empty
	if (words.length === 0) {
		// newWords is simply all the given characters
		for (var i = 0; i < characters.length; i++) {
			newWords.push( {name: characters[i], value: i} );
		}
	}
	else {
		// else, push combinations of the given words and additional characters
		for (var ii = 0; ii < words.length; ii++) {
			for (var jj = 0; jj < characters.length; jj++) {
				newWords.push( {name: words[ii].name + characters[jj], value: (ii*characters.length) + jj} );
			}
		}
	}

	// return the newly created expanded array
	return newWords;
}


// ------------------------------------------------------------------------------------
// User Controls
// ------------------------------------------------------------------------------------

function typeConstruction(string) {
	document.getElementById('constructionInput').value += string;
}

function clearConstruction() {
	document.getElementById('constructionInput').value = '';
	document.getElementById('wordsHTML').innerHTML = '';
}