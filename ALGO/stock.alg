<?xml version="1.0" encoding="UTF-8"?>
<Algo>
    <description texte="" courant="FIN_ALGORITHME"/>
    <extension extnom="inactif"/>
    <fonction fctetat="inactif" fctcode=""/>
    <F2 F2etat="inactif" F2lignes="" F2defaut="" F2para=""/>
    <repere repetat="inactif" repcode="-10#10#-10#10#2#2"/>
    <param epaisseurLigne="2" maxBoucle="500000" totalBoucles="5000000" nbDecimales="8" totalAffichages="1000" arrondiAuto="vrai" epaisseurPoint="2"/>
    <item algoitem="FONCTIONS_UTILISEES" code="200#declarationfonctions"/>
    <item algoitem="VARIABLES" code="100#declarationsvariables">
        <item algoitem="stock_initial EST_DU_TYPE NOMBRE" code="1#NOMBRE#stock_initial"/>
        <item algoitem="ajoutretrait EST_DU_TYPE NOMBRE" code="1#NOMBRE#ajoutretrait"/>
        <item algoitem="stock_final EST_DU_TYPE NOMBRE" code="1#NOMBRE#stock_final"/>
    </item>
    <item algoitem="DEBUT_ALGORITHME" code="101#debutalgo">
        <item algoitem="LIRE stock_initial" code="2#stock_initial#pasliste"/>
        <item algoitem="AFFICHER stock_initial" code="3#stock_initial#1#pasliste"/>
        <item algoitem="LIRE ajoutretrait" code="2#ajoutretrait#pasliste"/>
        <item algoitem="AFFICHERCALCUL stock_final=(stock_initial+ajoutretrait)" code="20#stock_final=(stock_initial+ajoutretrait)#1"/>
        <item algoitem="AFFICHER &quot;le stock final est à jour !&quot;" code="4#le stock final est à jour !#1"/>
    </item>
    <item algoitem="FIN_ALGORITHME" code="102#finalgo"/>
</Algo>
