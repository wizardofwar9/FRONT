document.getElementById("myBtn").addEventListener("click", displayDate);

function displayDate() {
    document.getElementById("date").innerHTML= Date();


let prenom = document.getElementById("prenom").value;
let nom = document.getElementById("nom").value;

document.getElementById("nom_prenom").innerHTML= (`le résultat est ${prenom} ${nom}`);

}

