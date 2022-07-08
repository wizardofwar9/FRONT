
 var MonTableau = []

MonTableau[0]="salomé"
MonTableau[1]="oceane"
MonTableau[2]="karim"
MonTableau[3]="jimmy"
MonTableau[4]="benoit"
MonTableau[5]="frederic"
MonTableau[6]="gregory"
MonTableau[7]="camille"
MonTableau[8]="anthony"
MonTableau[9]="cedric"
MonTableau[10]="stephane"
MonTableau[11]="perrine"
MonTableau[12]="theo"
 MonTableau[13]="outman"
 MonTableau[14]="halim"
 MonTableau[15]="zakaria"



/*
MonTableau.forEach(item => console.log(item));

for (i===10){breaks
}*/


/*en break ok
for (let i = 0; i < MonTableau.length; i++) {
    if (MonTableau[i]==="stephane"){
break;}
    
    console.log(MonTableau[i]);}

document.write(MonTableau);
/*backtick    ${dat}*/
/*`${1}`*/
/*continue qui fonctionne
for (let i = 0; i < MonTableau.length; i++) {
    


    if (MonTableau[i]==="stephane"){
    continue;}
        
        console.log(MonTableau[i]);}*/
        function list(){
       var input= document.getElementById("liste").value;

       for (let i = 0; i < MonTableau.length; i++) {

        if (MonTableau[i]==input){
            document.getElementById("result").innerHTML=MonTableau[i];
            return;
        }
        }
    
    document.getElementById("result").innerHTML=("pas das la liste");
    
      }      






