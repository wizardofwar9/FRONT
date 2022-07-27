<?xml version="1.0" encoding="UTF-8"?>
<Algo>
    <description courant="FIN_ALGORITHME" texte="compte des présences."/>
    <extension extnom="inactif"/>
    <fonction fctcode="" fctetat="inactif"/>
    <F2 F2para="" F2defaut="" F2etat="inactif" F2lignes=""/>
    <repere repetat="inactif" repcode="-10#10#-10#10#2#2"/>
    <param totalBoucles="5000000" totalAffichages="1000" maxBoucle="500000" nbDecimales="8" epaisseurLigne="2" epaisseurPoint="2" arrondiAuto="vrai"/>
    <item algoitem="FONCTIONS_UTILISEES" code="200#declarationfonctions"/>
    <item algoitem="VARIABLES" code="100#declarationsvariables">
        <item algoitem="greg EST_DU_TYPE NOMBRE" code="1#NOMBRE#greg"/>
        <item algoitem="steph EST_DU_TYPE NOMBRE" code="1#NOMBRE#steph"/>
        <item algoitem="outman EST_DU_TYPE NOMBRE" code="1#NOMBRE#outman"/>
        <item algoitem="theo EST_DU_TYPE NOMBRE" code="1#NOMBRE#theo"/>
        <item algoitem="cedric EST_DU_TYPE NOMBRE" code="1#NOMBRE#cedric"/>
        <item algoitem="Perrine EST_DU_TYPE NOMBRE" code="1#NOMBRE#Perrine"/>
        <item algoitem="absent EST_DU_TYPE NOMBRE" code="1#NOMBRE#absent"/>
        <item algoitem="présent EST_DU_TYPE NOMBRE" code="1#NOMBRE#présent"/>
        <item algoitem="compteur EST_DU_TYPE NOMBRE" code="1#NOMBRE#compteur"/>
        <item algoitem="somme EST_DU_TYPE NOMBRE" code="1#NOMBRE#somme"/>
    </item>
    <item algoitem="DEBUT_ALGORITHME" code="101#debutalgo">
        <item algoitem="greg PREND_LA_VALEUR 0" code="5#greg#0#pasliste"/>
        <item algoitem="steph PREND_LA_VALEUR 0" code="5#steph#0#pasliste"/>
        <item algoitem="outman PREND_LA_VALEUR 0" code="5#outman#0#pasliste"/>
        <item algoitem="theo PREND_LA_VALEUR 0" code="5#theo#0#pasliste"/>
        <item algoitem="cedric PREND_LA_VALEUR 0" code="5#cedric#0#pasliste"/>
        <item algoitem="Perrine PREND_LA_VALEUR 0" code="5#Perrine#0#pasliste"/>
        <item algoitem="absent PREND_LA_VALEUR 0" code="5#absent#0#pasliste"/>
        <item algoitem="présent PREND_LA_VALEUR 0" code="5#présent#0#pasliste"/>
        <item algoitem="compteur PREND_LA_VALEUR 0" code="5#compteur#0#pasliste"/>
        <item algoitem="AFFICHER &quot;Fiche de présence&quot;" code="4#Fiche de présence#1"/>
        <item algoitem="AFFICHER &quot; &quot;" code="4# #1"/>
        <item algoitem="AFFICHER &quot;Pour les présents tapez :1&quot;" code="4#Pour les présents tapez :1#1"/>
        <item algoitem="AFFICHER &quot;sinon 0&quot;" code="4#sinon 0#1"/>
        <item algoitem="" code="103#commentaire"/>
        <item algoitem="AFFICHER greg" code="3#greg#1#pasliste"/>
        <item algoitem="LIRE greg" code="2#greg#pasliste"/>
        <item algoitem="AFFICHER steph" code="3#steph#1#pasliste"/>
        <item algoitem="LIRE steph" code="2#steph#pasliste"/>
        <item algoitem="AFFICHER outman" code="3#outman#1#pasliste"/>
        <item algoitem="LIRE outman" code="2#outman#pasliste"/>
        <item algoitem="AFFICHER theo" code="3#theo#1#pasliste"/>
        <item algoitem="LIRE theo" code="2#theo#pasliste"/>
        <item algoitem="AFFICHER cedric" code="3#cedric#1#pasliste"/>
        <item algoitem="LIRE cedric" code="2#cedric#pasliste"/>
        <item algoitem="AFFICHER Perrine" code="3#Perrine#1#pasliste"/>
        <item algoitem="LIRE Perrine" code="2#Perrine#pasliste"/>
        <item algoitem="AFFICHER &quot;nombre d'élèves présents sur 6:&quot;" code="4#nombre d'élèves présents sur 6:#1"/>
        <item algoitem="AFFICHERCALCUL compteur=greg+steph+outman+theo+cedric+Perrine" code="20#compteur=greg+steph+outman+theo+cedric+Perrine#1"/>
        <item algoitem="SI (greg==1) ALORS" code="6#greg==1">
            <item algoitem="DEBUT_SI" code="7#debutsi"/>
            <item algoitem="AFFICHER &quot;greg présent&quot;" code="4#greg présent#1"/>
            <item algoitem="FIN_SI" code="8#finsi"/>
            <item algoitem="SINON" code="9#sinon">
                <item algoitem="DEBUT_SINON" code="10#debutsinon"/>
                <item algoitem="AFFICHER &quot;greg absent&quot;" code="4#greg absent#1"/>
                <item algoitem="FIN_SINON" code="11#finsinon"/>
            </item>
        </item>
        <item algoitem="SI (steph==1) ALORS" code="6#steph==1">
            <item algoitem="DEBUT_SI" code="7#debutsi"/>
            <item algoitem="AFFICHER &quot;steph est present&quot;" code="4#steph est present#1"/>
            <item algoitem="FIN_SI" code="8#finsi"/>
            <item algoitem="SINON" code="9#sinon">
                <item algoitem="DEBUT_SINON" code="10#debutsinon"/>
                <item algoitem="AFFICHER &quot;steph absent&quot;" code="4#steph absent#1"/>
                <item algoitem="FIN_SINON" code="11#finsinon"/>
            </item>
        </item>
        <item algoitem="SI (outman==1) ALORS" code="6#outman==1">
            <item algoitem="DEBUT_SI" code="7#debutsi"/>
            <item algoitem="AFFICHER &quot;outman présent&quot;" code="4#outman présent#1"/>
            <item algoitem="FIN_SI" code="8#finsi"/>
            <item algoitem="SINON" code="9#sinon">
                <item algoitem="DEBUT_SINON" code="10#debutsinon"/>
                <item algoitem="AFFICHER &quot;outman absent&quot;" code="4#outman absent#1"/>
                <item algoitem="FIN_SINON" code="11#finsinon"/>
            </item>
        </item>
        <item algoitem="SI (theo==1) ALORS" code="6#theo==1">
            <item algoitem="DEBUT_SI" code="7#debutsi"/>
            <item algoitem="AFFICHER &quot;theo présent&quot;" code="4#theo présent#1"/>
            <item algoitem="FIN_SI" code="8#finsi"/>
            <item algoitem="SINON" code="9#sinon">
                <item algoitem="DEBUT_SINON" code="10#debutsinon"/>
                <item algoitem="AFFICHER &quot;theo absent&quot;" code="4#theo absent#1"/>
                <item algoitem="FIN_SINON" code="11#finsinon"/>
            </item>
        </item>
        <item algoitem="SI (cedric==1) ALORS" code="6#cedric==1">
            <item algoitem="DEBUT_SI" code="7#debutsi"/>
            <item algoitem="AFFICHER &quot;cedric présent&quot;" code="4#cedric présent#1"/>
            <item algoitem="FIN_SI" code="8#finsi"/>
            <item algoitem="SINON" code="9#sinon">
                <item algoitem="DEBUT_SINON" code="10#debutsinon"/>
                <item algoitem="AFFICHER &quot;cedric absent&quot;" code="4#cedric absent#1"/>
                <item algoitem="FIN_SINON" code="11#finsinon"/>
            </item>
        </item>
        <item algoitem="SI (Perrine==1) ALORS" code="6#Perrine==1">
            <item algoitem="DEBUT_SI" code="7#debutsi"/>
            <item algoitem="AFFICHER &quot;perrine presente&quot;" code="4#perrine presente#1"/>
            <item algoitem="FIN_SI" code="8#finsi"/>
            <item algoitem="SINON" code="9#sinon">
                <item algoitem="DEBUT_SINON" code="10#debutsinon"/>
                <item algoitem="AFFICHER &quot;perrine absente&quot;" code="4#perrine absente#1"/>
                <item algoitem="FIN_SINON" code="11#finsinon"/>
            </item>
        </item>
    </item>
    <item algoitem="FIN_ALGORITHME" code="102#finalgo"/>
</Algo>
