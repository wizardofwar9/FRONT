const number = parseInt(prompt("entrer un nombre positif: "));
let isPrime = true;

if (number === 1) {
  document.write("false")
}

else if (number > 1) {
for (let i = 2; i < number; i++) {
  if (number % i == 0) {
    isPrime = false;
    break;
  }
}

if (isPrime) {
  document.write(`${number} true`);
} else {
  document.write(`${number} false`);
}
}
 else {
  document.write("false");
 }