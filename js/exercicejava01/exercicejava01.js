/*exo tableau
var tab = new Array()


tab [0] = "steph";
tab [1] = "greg";
tab [2] ="perrine";
tab [3] ="cedric";
tab [4] ="theo";
document.write(tab);
console.log(tab);
alert(Array);

document.write("Tableau d'origine : " + tab.join(", ") + "<BR>");
tab.sort()
document.write("Tri croissant : " + tab.join(", ") + "<BR>");
tab.reverse()
document.write("Tri décroissant : " + tab.join(", "));
document.write("  </br>");

*/

/*exercice date
var d = new Date();
document.write(" </br> ");
var date = d.getDate()+'-'+(d.getMonth()+1)+'-'+d.getFullYear();
document.write(date);
*/

/*les strings(essai)
let date1 = new Date();

let dateLocale = date1.toLocaleString('fr-FR',{
    weekday:'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric'});

    document.write(dateLocale);
    document.write(" </br></br>");
    dateLocale.toUpperCase();
    document.write(dateLocale.toUpperCase());
    
   let jourcourant = Day ;
document.write(day.toUpperCase());
*/
/*exrcice en cours*/ 
const ojourd8 = ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"];
const jourl=new Date();
var day = ojourd8[jourl.getDay()];
document.write("</br> Jour aujourd'hui: ", day,"</br>")
document.write(day.toUpperCase());

const sentence = 'The quick brown fox jumps over the lazy dog.';

const index = 4;

console.log(`The character at index ${index} is ${sentence.charAt(index)}`);
// expected output: "The character at index 4 is q"















