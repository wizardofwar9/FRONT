let form = document.querySelector('#loginForm');


form.email.addEventListener('change', function () {
  validEmail(this);
});
//regex pour email js
/*const regexEmail = (value) =>{
return /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(value);
}*/


//Ecouter la modification du pass word
form.password.addEventListener('change', function () {
    validPassword(this);
  });

//Validation EMAIL

const validEmail = function(inputEmail) {
    
    //Creation de la regex pour validation email
    let emailRegex = new RegExp(
        /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/,
        'g'
    );


    let testEmail = emailRegex.test(inputEmail.value);
    //On recupere de la balise small 
let small = inputEmail.nextElementSibling;
//on teste l'expression reguliaire
    if(testEmail) {
small.innerHTML = 'Adresse valide';
small.classList.remove('text-danger');
small.classList.add('test-success');  
}
    else {
        small.innerHTML = 'Adresse Non valide';
small.classList.remove('test-success');
small.classList.add('test-danger');
    }
};

//Validation du password
const validPassword = function(inputPassword) {
    let msg;
    let valid = false;
//au moins 3 carateres
if(inputPassword.value.length < 3){
    msg = "Veuillez renseigner un code d au moins 3 caracteres";
}
//au moin 1 maj 
else if(!/[A-Z/].test(inputPassword.value)){
    msg = "Veuillez renseigner un code d au moins 1 majuscule";
}
//au moin 1 min};
else if(!/[a-z/].test(inputPassword.value)){
    msg = "Veuillez renseigner un code d au moins 1 minuscule";
}
//au moins un chiffre
else if(!/[0-9/].test(inputPassword.value)){
    msg = "Veuillez renseigner au moins un chiffre";
}
    
    else if(inputPassword.value.length < 6){
        msg = "Veuillez renseigner un code d un chiffre

//MDP Valide
