<?xml version="1.0" encoding="UTF-8"?>
<Algo>
    <description texte="&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;" courant="FIN_ALGORITHME"/>
    <extension extnom="inactif"/>
    <fonction fctcode="" fctetat="inactif"/>
    <F2 F2lignes="" F2para="" F2etat="inactif" F2defaut=""/>
    <repere repcode="-10#10#-10#10#2#2" repetat="inactif"/>
    <param nbDecimales="8" arrondiAuto="vrai" maxBoucle="500000" totalAffichages="1000" totalBoucles="5000000" epaisseurLigne="2" epaisseurPoint="2"/>
    <item algoitem="FONCTIONS_UTILISEES" code="200#declarationfonctions"/>
    <item algoitem="VARIABLES" code="100#declarationsvariables">
        <item algoitem="i EST_DU_TYPE NOMBRE" code="1#NOMBRE#i"/>
        <item algoitem="t EST_DU_TYPE LISTE" code="1#LISTE#t"/>
    </item>
    <item algoitem="DEBUT_ALGORITHME" code="101#debutalgo">
        <item algoitem="POUR i ALLANT_DE 0 A 9" code="12#i#0#9">
            <item algoitem="DEBUT_POUR" code="13#debutpour"/>
            <item algoitem="AFFICHER &quot;Saisir une valeur:&quot;" code="4#Saisir une valeur:#1"/>
            <item algoitem="LIRE t[(i)]" code="2#t#(i)"/>
            <item algoitem="AFFICHER &quot;****************&quot;" code="4#****************#1"/>
            <item algoitem="FIN_POUR" code="14#finpour"/>
        </item>
        <item algoitem="AFFICHER &quot;*****Affichage des elements du tableau*******&quot;" code="4#*****Affichage des elements du tableau*******#1"/>
        <item algoitem="AFFICHER &quot;valeur de l indice i est de :&quot;" code="4#valeur de l indice i est de :#1"/>
        <item algoitem="AFFICHER i" code="3#i#1#pasliste"/>
        <item algoitem="i PREND_LA_VALEUR 0" code="5#i#0#pasliste"/>
        <item algoitem="TANT_QUE (i&lt;10) FAIRE" code="15#i&lt;10">
            <item algoitem="DEBUT_TANT_QUE" code="16#debuttantque"/>
            <item algoitem="AFFICHER &quot;valeur:&quot;" code="4#valeur:#1"/>
            <item algoitem="AFFICHER t[i]" code="3#t#1#i"/>
            <item algoitem="i PREND_LA_VALEUR i+1" code="5#i#i+1#pasliste"/>
            <item algoitem="FIN_TANT_QUE" code="17#fintantque"/>
        </item>
        <item algoitem="i PREND_LA_VALEUR 0" code="5#i#0#pasliste"/>
    </item>
    <item algoitem="FIN_ALGORITHME" code="102#finalgo"/>
</Algo>
