const value = Math.PI;
const decimals = 2;
const factor = Math.pow(10, decimals);
let rounded = Math.round(value * factor) / factor;
console.log(rounded);

const a = 3;
const b = 4;
let hypotenuse = Math.sqrt(Math.pow(a, 2) + Math.pow(b, 2));
console.log(hypotenuse);

console.log(`floor: ${Math.floor(-2.5)}, ceil: ${Math.ceil(-2.5)}, round: ${Math.round(-2.5)}`);
//Hacemos que le numero se redonde hasta donde podamos y entramos de nuevo
