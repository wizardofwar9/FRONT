let prixboisson = prompt("Prix boisson")
let quantiteboisson = prompt("Quantité boisson");
document.write("Le prix pour vos boissons est de ");
document.write(prixboisson * quantiteboisson);
document.write(" € </br></br>");


let prixplat = prompt("Prix plat");
let quantiteplat = prompt("Quantité plat");
document.write("Le prix pour vos plats est de ");
document.write(prixplat * quantiteplat);
document.write(" € </br></br>");


let prixsupplement = prompt("Prix supplément");
let quantitesupplement = prompt("Quantité supplément");
document.write("Le prix pour vos suppléments est de ");
document.write(prixsupplement * quantitesupplement);
document.write(" € </br></br>");


let totalht = (prixboisson*quantiteboisson+prixplat*quantiteplat+prixsupplement*quantitesupplement);
document.write("Total H.T ");
document.write(totalht);
document.write(" € </br></br>");


let tva = prompt("Entrez TVA");
document.write("TTC ");
let totalttc = totalht * tva;
document.write(totalttc);
document.write(" € </br></br>");
