document.write("Nous sommes le : ");
var d = new Date();

var date = d.getDate()+'-'+(d.getMonth()+1)+'-'+d.getFullYear();
document.write(date);
document.write(" </br>");

document.write("Fiche de presence:</br>");
document.write("Pour les présents tapez ok:</br>");
document.write("sinon pour les absents Annuler:</br>");
document.write("</br></br>");
document.write("</br></br>");

var totalpresents = 0
var totalabsents = 0
var presents = true
var absents = false


var greg = confirm("greg ?");
/*document.write(greg);
document.write(" greg en classe ! </br></br>");*/

if( greg == true) {
    document.write("  greg en classe ! </br></br>"); 
      totalpresents = totalpresents +1
     
document.write(" </br></br>")

} else {
    document.write(" greg est absent ! </br></br>");
     totalabsents = totalabsents +1
     
document.write(" </br></br>")
} 



var steph = confirm("steph ?");
/*document.write(steph);
document.write(" steph en classe ! </br></br>");*/

    if( steph == true) {
        document.write(" steph en classe ! </br></br>");  
         totalpresents = totalpresents +1
        
document.write(" </br></br>")
    } else {
        document.write(" steph est absent ! </br></br>");
         totalabsents = totalabsents +1 
        
document.write(" </br></br>")
    }
    //insertion test pour zak en ternaire
 let zak = confirm("zak est il présent?");
   
 zak == true ?
  (document.write("zak en classe ! </br></br>"),
 (totalpresents = totalpresents +1)):
 (document.write(" zak est absent ! </br></br>")
  (totalabsents = totalabsents +1));
            
 document.write(" </br></br>")
   
    
    var perrine = confirm("perrine ?");
/*document.write(perrine);
document.write(" perrine en classe ! </br></br>");*/

    if( perrine == true) {
        document.write(" Perrine en classe ! </br></br>");  
         totalpresents = totalpresents +1
        
document.write(" </br></br>")
    } else {
        document.write(" Perrine est absent ! </br></br>");
         totalabsents = totalabsents +1
        
document.write(" </br></br>")
    }
document.write("----------------------</br></br>");
document.write("total présents :</br>");
document.write(totalpresents);
document.write("</br></br>");
document.write("total absents : </br>");
document.write(totalabsents);
document.write( "  </br></br>");


document.write(" ");
document.write("document effectué à : ")
var d = new Date();
var hours = d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
document.write(hours);


















