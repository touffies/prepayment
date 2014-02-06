PLUGIN PREPAYMENT POUR THELIA 1.5
--------------------------------------

Ce plugin permet l'utilisation de compte prépayé. Il suffie de créer une caractéristique
et de l'associer à un prepayment en spécifiant si vous voulez utiliser le prix ou la quantité
pour gérer les crédits.

IMPORTANT : Ce plugin utilise le plugin LIVRAISON_ZERO afin d'annuler les frais de livraison pour
les produits de type "Rechargement de crédit".

Auteurs :

   - Christophe LAFFONT - Openstudio / www.openstudio.fr

INSTALLATION
------------

Pour installer ce plugin, il vous faut :
1. Installer le plugin `prepayment` dans le dossier `/client/plugins/` de votre site.
2. Activer ce plugin dans le menu configuration -> Activation des plugins.
3. Se rendre dans Modules -> Module de prépaiement -> Configuration du site afin d'associer une caractéristique à un prépaiement.


LES BOUCLES
-----------

Le plugin propose deux boucles. Ces boucles sont accessibles de la façon suivante:

<THELIA_<nomboucle> type="PREPAYMENT" boucle="<nom_boucle>" paramètres....>

Le paramètre "boucle" permet de désigner la boucle a exécuter.

1) Boucle transport
   ----------------

Cette boucle doit être utilisée autour de la boucle TRANSPORT de Thelia. Elle
permet d'annuler les frais de livraisn dans la cas ou le panier ne comporte que
des produits pour recharger un compte (Produits associés à un prepayment).

Paramètres:

   id : Identifiant d'un plugin de transport
   exlcusion :  Liste de nom à exclure, sépraré par une vigule ","

Variables

   #ID : Identifiant du plugin de transport
   #EXCLUSION : Liste de nom à exclure, sépraré par une vigule ","

Exemple d'utilisation :

	<div class="choixDeLaLivraison">
        <ul>
            <THELIA_prepayement type="PREPAYMENT" boucle="transport">
                <THELIA_transport type="PREPAYMENT" boucle="transport" exclusion="#EXCLUSION" id="#ID">
                    <li><a href="#URLCMD"><span class="modeDeLivraison">#TITRE / #PORT €</span></a></li>
                </THELIA_transport>
            </THELIA_prepayement>
        </ul>
    </div>


2) Boucle paiement
   ---------------

Cette boucle permet de proposer le type de paiement prépayé au client en fonction du type de produit
dans le panier. On vérifie également que le client a suffisamment de crédit.

Paramètres:

   N/A

Variables:

   #EXCLUSION  : Liste des modules de paiement à exclure (l'exclusion se fait sur le nom, exemple : exclusion="atos,cheque" )


Exemples d'utilisation, pour afficher la liste des paiements sur la page commande :

    <h2>::choixmodepaiement:: </h2>
    <div class="choixDuReglement">
        <ul>
            <THELIA_prepayment type="PREPAYMENT" boucle="paiement">
                <THELIA_PAIEMENT type="PAIEMENT" exclusion="#EXCLUSION">
                    <li><a href="#URLPAYER"><span class="modeDeReglement">#TITRE</span><span class="choisir"></span></a></li>
                </THELIA_PAIEMENT>
            </THELIA_prepayment>
        </ul>
    </div>


CHANGELOG
------------

= 1.0.0 (06/02/2014) =
* Première version du plugin



@TODO
------------

* Revoir toutes les méthodes Destroy
* Tester et vérifier le prépaiement de type prix (Devise, Frais de port et les devises)
