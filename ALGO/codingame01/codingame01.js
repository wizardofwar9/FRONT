/*code qui change le h1 dans le html
var link = document.getElementsByTagName('h1')[0];

var newLabel= document.createTextNode('Rick Astley - Never Gonna Give You Up');


link.replaceChild(newLabel, link.firstChild);
/*code qui enleve le br et la ligne du 1er couplet*/


    /*var couplet = document.getElementsByClassName("couplet")[1];
   
couplet.removeChild(couplet.firstChild);*/

/*
var elements = document.getElementsByClassName("couplet");
for(var i = 0; i < elements.length; i++) {
      couplet.removeChild(couplet.firstChild);
}*/

document.body.firstElementChild.innerHTML = "Rick Asley - Never gonna give you up";



const coupl = document.getElementsByClassName("couplet")



for (let i = 0; i < coupl.length; i++) {

coupl[i].removeChild(coupl[i].firstChild);

}



const refrain = document.getElementsByClassName("couplet refrain");



for (let x = 0; x < refrain.length; x++) {

const refrainChildren = refrain[x].childNodes;

const refrainNUmberChildren = Math.round(refrainChildren.length / 2);



for (let y = 0; y < refrainNUmberChildren; y += 2) {

for (let i = 0; i < 2; i++) {

refrain[x].removeChild(refrainChildren[y]);

}

}

}



const err = document.getElementById("erreur")

err.remove("erreur")



const footer = document.createElement("footer");

footer.innerText = "© Copyright 2020 - Nom";

document.body.appendChild(footer);





    
  