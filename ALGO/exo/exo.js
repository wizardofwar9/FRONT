let menu = prompt("operations");
menu = parseInt(menu)

switch (menu){
    case 1 :
        alert("faire un retrait");
        document.write("faire un retrait")
        break;
        case 2 :
            alert("consulter mon solde");
            document.write("consulter mon solde")
            break; 
        case 3 :    
                alert("faire un virement");
                document.write("faire un virement")
                break;   
        case 4 :
            alert("faire un depôt");
            document.write("faire un depôt")
            break; 
            default :
            alert("pas de valeur")  ;  
}
