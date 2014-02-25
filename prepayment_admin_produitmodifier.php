<?php
/*************************************************************************************/
/*                                                                                   */
/*      Module de prépaiement pour Thelia	                                         */
/*                                                                                   */
/*      Copyright (c) Openstudio 		                                     		 */
/*      Développement : Christophe LAFFONT		                                     */
/*		email : claffont@openstudio.fr	        	                             	 */
/*      web : http://www.openstudio.fr					   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/

// On s'assure que la constante __DIR__ est définie pour les versions de PHP antérieur à 5.3
(@__DIR__ == '__DIR__') && define('__DIR__', realpath(dirname(__FILE__)));

// Vérifier authorisation
include_once __DIR__ . "/../../../fonctions/authplugins.php";
autorisation("prepayment");
?>

<?php
// On vérifie qu'on a bien une référence produit
$ref = trim(lireParam("ref", "string"));

if( $ref != "")
{
    // On charge les infos du produit
    $prod = new Produit();
    $prod->charger($ref);

    // On vérifie si ce produit peut utiliser la méthode de paiement prépayé
    $prepayment_produit = new Prepayment_produit();
    $prepayment_produit->charger_produit($prod->id);
?>

<!-- début du bloc de Prepayment -->
<a name="prepayement"></a>

<div class="entete">
    <div class="titre" style="cursor:pointer" onclick="$('#pliantprepayement').show('slow');"><?php echo trad('Prépaiement', 'prepayment'); ?></div>
</div>
<div id="pliantprepayement" class="blocs_pliants_prod">
    <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: separate;margin: 0;">
        <tbody>
            <tr class="claire" style="height: auto;">
                <td class="designation"><?php echo trad('Caractéristique', 'prepayment'); ?><br> <span class="note"><?php echo trad('Caractéristique utilisée pour le prépaiement.', 'prepayment'); ?></span></td>
                <td>
                    <select name="select_prepayment" class="form_long">
                        <option value=""><?php echo trad('Ne pas utiliser de paiement prépayé', 'prepayment'); ?></option>
                        <?php
                        $prepayment = new Prepayment();
                        $res_prepayment = $prepayment->query_liste("SELECT * FROM $prepayment->table");
                        $selected_found = false;
                        foreach($res_prepayment as $row)
                        {
                            $caracteristiquedesc = new Caracteristiquedesc($row->caracteristique_id);
                            if(!empty($caracteristiquedesc))
                            {
                                if ($row->id == $prepayment_produit->prepayment_id){
                                    $selected_found = true;
                                    $selected = ' selected="selected"';
                                }else{
                                    $selected = '';
                                }

                                echo '<option value="'. $row->id .'"'.$selected.'>'.$caracteristiquedesc->titre.'</option>';
                            }
                        }
                        ?>
                    </select>
                    <?php
                    // Afficher un message si le produit est associé à une caractéristique qui n'existe plus
                    if(intval($prepayment_produit->prepayment_id) > 0 && !$selected_found){
                        echo trad('<small style="color:red">Attention : ce produit est associé à une caractéristique de prépaiement qui n\'existe plus.</small>');
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantprepayement').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>
<!-- fin du bloc de Prepayment -->
<?php } ?>