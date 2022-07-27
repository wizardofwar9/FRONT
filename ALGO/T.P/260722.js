//localStorage.setItem("clé",valeur)
//localStorage.getItem("clé")
//localStorage.removeItem("clé")
//localStorage.clear()
//JSON.stringify(objet)
//JSON.parse(string)


const local = JSON.parse(localStorage.getItem("user"));

if (local!== null)
  { 
    formulaire.style.display = "none";
    // si le nom est null alors h1 va mettre le nom qui est rester dans le localStorage
  h1.textContent = `Bonjour ${local.nom} tu as ${local.age} ans`;
  }

bouton.onclick = () =>{
  const user = {
    //créé un objets pour le nom et l'âge
    nom: nom.value,
    age: age.value,
  };
  //localStorage.setItem("nom",nom.value);  c'est pour stock la clé et la valeur de mon input nom / nom.value = la valeur de mon input nom
  localStorage.setItem("user",JSON.stringify(user)); //
  document.location.reload();
};

clear.onclick = () =>{
  localStorage.clear(); //pour supprimer le storage
  document.location.reload();
};