function Calculer() {

    var stk_bois = 10;
    var stk_plats = 8;
    var stk_supp = 6;

    if (document.getElementById("qt_bois").value > stk_bois) {
        alert("Stock insuffisant");
        /*document.getElementById("qt_bois").style.backgroundColor = "red";*/
    }

    else if (document.getElementById("qt_plats").value > stk_plats) {
        alert("Stock insuffisant");
        /*document.getElementById("qt_plats").style.backgroundColor = "red";*/
    }

    else if (document.getElementById("qt_supp").value > stk_supp) {
        alert("Stock insuffisant");
        /*document.getElementById("qt_supp").style.backgroundColor = "red";*/            /*produit limité en stock couleur de fond rouge*/
    }

    else {
        document.getElementById("ttc_bois").value = document.getElementById("qt_bois").value * document.getElementById("pu_bois").value;
        document.getElementById("ttc_plats").value = document.getElementById("qt_plats").value * document.getElementById("pu_plats").value;
        document.getElementById("ttc_supp").value = document.getElementById("qt_supp").value * document.getElementById("pu_supp").value;

        document.getElementById("TotTTC").value = parseFloat(document.getElementById("ttc_bois").value) + parseFloat(document.getElementById("ttc_plats").value) + parseFloat(document.getElementById("ttc_supp").value);
        document.getElementById("TotHT").value = (((document.getElementById("TotTTC").value)/1.1).toFixed(2));
        document.getElementById("TVA").value = (((document.getElementById("TotTTC").value) - ((document.getElementById("TotHT").value))).toFixed(2));
    }
}

