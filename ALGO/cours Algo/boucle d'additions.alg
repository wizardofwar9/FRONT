<?xml version="1.0" encoding="UTF-8"?>
<Algo>
    <description courant="FIN_ALGORITHME" texte=""/>
    <extension extnom="inactif"/>
    <fonction fctcode="" fctetat="inactif"/>
    <F2 F2para="" F2defaut="" F2lignes="" F2etat="inactif"/>
    <repere repcode="-10#10#-10#10#2#2" repetat="inactif"/>
    <param epaisseurPoint="2" maxBoucle="500000" arrondiAuto="vrai" totalBoucles="5000000" epaisseurLigne="2" totalAffichages="1000" nbDecimales="8"/>
    <item algoitem="FONCTIONS_UTILISEES" code="200#declarationfonctions"/>
    <item algoitem="VARIABLES" code="100#declarationsvariables">
        <item algoitem="nb EST_DU_TYPE NOMBRE" code="1#NOMBRE#nb"/>
        <item algoitem="somme EST_DU_TYPE NOMBRE" code="1#NOMBRE#somme"/>
        <item algoitem="compteur EST_DU_TYPE NOMBRE" code="1#NOMBRE#compteur"/>
    </item>
    <item algoitem="DEBUT_ALGORITHME" code="101#debutalgo">
        <item algoitem="//Boucle pour la saisie des valeurs" code="19#Boucle pour la saisie des valeurs"/>
        <item algoitem="POUR compteur ALLANT_DE 1 A 5" code="12#compteur#1#5">
            <item algoitem="DEBUT_POUR" code="13#debutpour"/>
            <item algoitem="AFFICHER &quot;entrez un nombre entier&quot;" code="4#entrez un nombre entier#0"/>
            <item algoitem="LIRE nb" code="2#nb#pasliste"/>
            <item algoitem="//addition des valeurs saisies" code="19#addition des valeurs saisies"/>
            <item algoitem="somme PREND_LA_VALEUR (somme+ nb)" code="5#somme#(somme+ nb)#pasliste"/>
            <item algoitem="FIN_POUR" code="14#finpour"/>
        </item>
        <item algoitem="AFFICHER &quot;le total des valeurs saisies est:&quot;" code="4#le total des valeurs saisies est:#0"/>
        <item algoitem="AFFICHER somme" code="3#somme#0#pasliste"/>
    </item>
    <item algoitem="FIN_ALGORITHME" code="102#finalgo"/>
</Algo>
