



function tester() {
    let expr = document.querySelector('p');

    let r1 = /Outman/;
    let res1 = expr.textContent.match(r1);

    let resultat = document.getElementById('reg');
    // resultat.innerHTML = 'Résultat match() sur regex 1 :' + res1;
    resultat.innerHTML = `Le mot est bien dans la phrase: ${res1}`
};

