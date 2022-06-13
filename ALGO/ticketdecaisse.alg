<?xml version="1.0" encoding="UTF-8"?>
<Algo>
    <description courant="AFFICHER TVA" texte=""/>
    <extension extnom="inactif"/>
    <fonction fctcode="" fctetat="inactif"/>
    <F2 F2etat="inactif" F2lignes="" F2defaut="" F2para=""/>
    <repere repetat="inactif" repcode="-10#10#-10#10#2#2"/>
    <param epaisseurLigne="2" epaisseurPoint="2" arrondiAuto="vrai" totalBoucles="5000000" nbDecimales="8" maxBoucle="500000" totalAffichages="1000"/>
    <item algoitem="FONCTIONS_UTILISEES" code="200#declarationfonctions"/>
    <item algoitem="VARIABLES" code="100#declarationsvariables">
        <item algoitem="trentetroisclperrier EST_DU_TYPE NOMBRE" code="1#NOMBRE#trentetroisclperrier"/>
        <item algoitem="salade_chef EST_DU_TYPE NOMBRE" code="1#NOMBRE#salade_chef"/>
        <item algoitem="supplement_firtes EST_DU_TYPE NOMBRE" code="1#NOMBRE#supplement_firtes"/>
        <item algoitem="total EST_DU_TYPE NOMBRE" code="1#NOMBRE#total"/>
        <item algoitem="ht EST_DU_TYPE NOMBRE" code="1#NOMBRE#ht"/>
        <item algoitem="TVA EST_DU_TYPE NOMBRE" code="1#NOMBRE#TVA"/>
    </item>
    <item algoitem="DEBUT_ALGORITHME" code="101#debutalgo">
        <item algoitem="trentetroisclperrier PREND_LA_VALEUR 3" code="5#trentetroisclperrier#3#pasliste"/>
        <item algoitem="salade_chef PREND_LA_VALEUR 10" code="5#salade_chef#10#pasliste"/>
        <item algoitem="supplement_firtes PREND_LA_VALEUR 2.5" code="5#supplement_firtes#2.5#pasliste"/>
        <item algoitem="AFFICHERCALCUL total=trentetroisclperrier+salade_chef+supplement_firtes" code="20#total=trentetroisclperrier+salade_chef+supplement_firtes#0"/>
        <item algoitem="AFFICHER total" code="3#total#1#pasliste"/>
        <item algoitem="AFFICHERCALCUL ht=total-((total*10)/100)" code="20#ht=total-((total*10)/100)#0"/>
        <item algoitem="AFFICHER ht" code="3#ht#1#pasliste"/>
        <item algoitem="AFFICHERCALCUL TVA=total-ht" code="20#TVA=total-ht#0"/>
        <item algoitem="AFFICHER TVA" code="3#TVA#1#pasliste"/>
    </item>
    <item algoitem="FIN_ALGORITHME" code="102#finalgo"/>
</Algo>
