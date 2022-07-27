<?xml version="1.0" encoding="UTF-8"?>
<Algo>
    <description texte="" courant="FIN_ALGORITHME"/>
    <extension extnom="inactif"/>
    <fonction fctetat="inactif" fctcode=""/>
    <F2 F2para="" F2lignes="" F2defaut="" F2etat="inactif"/>
    <repere repcode="-10#10#-10#10#2#2" repetat="inactif"/>
    <param arrondiAuto="vrai" totalBoucles="5000000" maxBoucle="500000" epaisseurPoint="2" nbDecimales="8" epaisseurLigne="2" totalAffichages="1000"/>
    <item code="200#declarationfonctions" algoitem="FONCTIONS_UTILISEES"/>
    <item code="100#declarationsvariables" algoitem="VARIABLES">
        <item code="1#NOMBRE#x" algoitem="x EST_DU_TYPE NOMBRE"/>
        <item code="1#NOMBRE#racine" algoitem="racine EST_DU_TYPE NOMBRE"/>
    </item>
    <item code="101#debutalgo" algoitem="DEBUT_ALGORITHME">
        <item code="2#x#pasliste" algoitem="LIRE x"/>
        <item code="6#x>=0" algoitem="SI (x>=0) ALORS">
            <item code="7#debutsi" algoitem="DEBUT_SI"/>
            <item code="5#racine#sqrt(x)#pasliste" algoitem="racine PREND_LA_VALEUR sqrt(x)"/>
            <item code="3#racine#0#pasliste" algoitem="AFFICHER racine"/>
            <item code="8#finsi" algoitem="FIN_SI"/>
        </item>
    </item>
    <item code="102#finalgo" algoitem="FIN_ALGORITHME"/>
</Algo>
