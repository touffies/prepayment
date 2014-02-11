PLUGIN DE PREPAIEMENT POUR THELIA 1.5
--------------------------------------

Ce plugin permet l'utilisation de compte prépayé. Il suffie de créer une caractéristique
et de l'associer à un prepayment en spécifiant si vous voulez utiliser le prix ou la quantité
pour gérer les crédits.

**IMPORTANT :** Ce plugin utilise le plugin [LIVRAISON_ZERO][1] afin d'annuler les frais de livraison pour
les produits de type **Rechargement de crédit**.


> **Auteur**
>
>   Christophe LAFFONT - Openstudio / [www.openstudio.fr][2]


INSTALLATION
---------

Pour installer ce plugin, il vous faut :

 1. Installer le plugin `prepayment` dans le dossier `/client/plugins/` de votre site.
 2. Activer ce plugin dans le menu `Configuration -> Activation des plugins`.
 3. Se rendre dans `Configuration -> Gestion des caractéristiques` pour ajouter une nouvelle caractéristique. (Exemple : Nombre de films)
 3. Se rendre dans `Modules -> Module de prépaiement -> Connection au site` du site afin d'associer cette caractéristique à un prépaiement.
 4. Ajouter une nouvelle rubrique `Recharge de crédit` et lui associer la caractéristique sélectionnée à l'étape précédente.
 5. Créer des produits dans cette rubrique et renseignez le champ pour cette caractéristique. (C'est cette valeur qui sera utilisée pour créditer ou débiter votre compte de prépaiement)
 6. Pour finir, si vous voulez qu'un client puisse payer un produit en utilisant son compte prépayé, vous devez sélectionner la caractéristique de `Prépaiement` à la création du produit.

LES BOUCLES
---------

Le plugin propose ***trois boucles***. Ces boucles sont accessibles de la façon suivante:

```
<THELIA_<nomboucle> type="VODINFOMANIAK" boucle="<nom_boucle>" paramètres....>
```
Le paramètre "boucle" permet de désigner la boucle a exécuter.


1) **Boucle transport**

Cette boucle doit être utilisée autour de la boucle TRANSPORT de Thelia. Elle
permet d'annuler les frais de livraison dans la cas ou le panier ne comporte que
des produits pour recharger un compte (Produits associés à un prepayment).

**Paramètres:**

 - id : Identifiant d'un plugin de transport
 - exlcusion :  Liste de nom à exclure, sépraré par une virgule ","

**Variables:**

```
   #ID : Identifiant du plugin de transport
   #EXCLUSION : Liste de nom à exclure, sépraré par une virgule ","
```

Exemple d'utilisation :

```
<div class="choixDeLaLivraison">
    <ul>
        <THELIA_prepayment type="PREPAYMENT" boucle="transport">
            <THELIA_transport type="PREPAYMENT" boucle="transport" exclusion="#EXCLUSION" id="#ID">
                <li><a href="#URLCMD"><span class="modeDeLivraison">#TITRE / #PORT €</span></a></li>
            </THELIA_transport>
        </THELIA_prepayment>
    </ul>
</div>
```


2) **Boucle paiement**

Cette boucle permet de proposer le type de paiement prépayé au client en fonction du type de produit dans le panier. On vérifie également que le client a suffisamment de crédit.

**Paramètres:**

 - Aucun

**Variables:**

```
#EXCLUSION  : Liste des modules de paiement à exclure (l'exclusion se fait sur le nom, exemple : exclusion="atos,cheque" )
```

Exemples d'utilisation, pour afficher la liste des vidéos en location sur la page `commande.html` :

```
<h2>::choixmodepaiement:: </h2>
<div class="choixDuReglement">
    <ul>
        <THELIA_prepayment type="PREPAYMENT" boucle="paiement">
            <THELIA_PAIEMENT type="PAIEMENT" exclusion="#EXCLUSION">
                <li><a href="#URLPAYER"><span class="modeDeReglement">#TITRE</span><span class="choisir"></span></a>
            </THELIA_PAIEMENT>
        </THELIA_prepayment>
    </ul>
</div>
```

3) **Boucle Defaut**

Cette boucle permet d'afficher le nombre de crédit d'un client.

**Paramètres:**

 - client_id : Identifiant d'un client
 - prepayment_id : Identifiant du prepayment
 - module : Nom du module, permet de filtrer les modules de paiement et d'afficher le nombre de crédit restant pour le module de Prepayment uniquement (Inclusion dans la boucle Paiement)

**Variables:**

```
#LABEL        : Libéllé du type de Prepayment (On utilise la description d'une caractéristique)
#CREDIT       : Nombre de crédit restant
```

Exemple d'utilisation, pour visualiser le nombre de crédit restant sur la page `moncompte.html` et proposer de recharger :

```
<!-- Prepayment -->
<T_prepayment>
    <div id="credit">
        <h2>Mon crédit VOD</h2>
        <table id="table-credit">
            <tbody>
                <tr>
                    <td class="ligne">
                    <THELIA_prepayment type="PREPAYMENT" client="#CLIENT_ID" prepayment="1">
                        Crédit VOD restant : #CREDIT film(s)
                    </THELIA_prepayment>
                    </td>
                    <td class="ligne"><a href="#URLFOND(recharge)">Recharger</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</T_prepayment>
<//T_prepayment>
```

QUESTIONS FREQUENTES
---------

**J'ai ajouté une vidéo dans mon panier, mais je ne vois pas le paiement `Débiter mon compte prépayé` ?**
Pour pouvoir utiliser le paiement par compte prépayé, vous devez vous assurer que le produit ajouté dans le panier et bien associer avec une méthode de **Prepayment**.
Le paiement `Débiter mon compte prépayé` ne vous sera proposé que si votre compte contient suffisamment de crédit ou d'argent.

**Je voudrais modifier les urls de retour en cas de succès ou d'erreur de paiement**

Par défaut, le plugin vous retourne à la page `merci.html` et `regret.html`, mais vous pouvez modifier ces urls dans le fichier `config.php`.

**Je voudrais autoriser un montant négatif**

vous pouvez modifier le montant minimum, en modifiant la valeur de la constante `PREPAYMENT_MINIMUM` dans le fichier `config.php`.


----------

CHANGELOG
---------

- **1.0.2** (11/02/2014) - Ajout d'une constante pour filtrer les statuts (Non Payé et Annulé)
- **1.0.1** (10/02/2014) - Ajout du fichier Readme.md (Markdown)
- **1.0.0** (06/02/2014) - Première version du plugin


@TODO
---------

* Revoir toutes les méthodes Destroy
* Tester et vérifier le prépaiement de type prix (Devise, Frais de port et les devises)


[1]: https://github.com/touffies/livraison_zero
[2]: http://www.openstudio.fr

