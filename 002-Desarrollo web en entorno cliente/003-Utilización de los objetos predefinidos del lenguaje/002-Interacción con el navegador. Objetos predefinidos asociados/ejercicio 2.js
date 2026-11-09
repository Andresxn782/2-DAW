let fullName = 'aNA maRÍA lóPEZ';
fullName.toUpperCase();
console.log(fullName);

const words = fullName.split(' ');

for (let i = 0; i < words.length; i++) {
  const word = words[i];
  words[i] = word.charAt(0).toUpperCase() + word.substring(1).toLowerCase();
}

fullName = words.join(' ');

console.log(fullName);
// Añadimos la mayusculas al las letras que estan en misnusculas
