<?php
/* ----------------------------------------------------------------------
 * app/views/client/order_item_list_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source places management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011-2012 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
	$t_order = $this->getVar('t_order');
	$vn_order_id = (int)$t_order->getPrimaryKey();
	$vn_transaction_id = $this->getVar('transaction_id');
	$va_errors = $this->getVar('errors');
	$t_order_item = new ca_commerce_order_items();
	
	$vs_currency_symbol = $this->getVar('currency_symbol');
	$vs_currency_input_format = "<div class='formLabel'>^LABEL<br/>{$vs_currency_symbol}^ELEMENT</div>";
	
	$vs_id_prefix = 'item_list';
	
	$va_initial_values = $this->getVar('order_items');
	
	$va_additional_fee_template_codes = array();
	if (is_array($va_additional_fee_codes = $this->getVar('additional_fee_codes'))) {
		foreach($va_additional_fee_codes as $vs_code => $va_info) {
			$va_additional_fee_template_codes[] = "'ADDITIONAL_FEE_{$vs_code}'";
		}
	}
	
	print $vs_control_box = caFormControlBox(
		(caFormSubmitButton($this->request, __CA_NAV_BUTTON_SAVE__, _t("Save"), 'caClientOrderItemListForm')).' '.
		(caNavButton($this->request, __CA_NAV_BUTTON_CANCEL__, _t("Cancel"), 'client', 'OrderEditor', 'Shipping', array('order_id' => $vn_order_id))),
		'', 
		(caNavButton($this->request, __CA_NAV_BUTTON_DELETE__, _t("Delete"), 'client', 'OrderEditor', 'Delete', array('order_id' => $vn_order_id)))
	);
	
	print caFormTag($this->request, 'SaveItemList', 'caClientOrderItemListForm', null, 'post', 'multipart/form-data', '_top', array());
?>
<h1><?php print _t('Items Ordered'); ?></h1>

<div id="<?php print $vs_id_prefix.'_item'; ?>">
<?php
	//
	// Template to generate display for existing items
	//
?>
	<textarea class='caItemTemplate' style='display: none;'>
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo sortableOrderItem">
			<a href="#" class="caDeleteItemButton" style="float: right;"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__); ?></a>
								
			<table>
				<tr>
					<td colspan='2'>
						<div style="font-size: 14px; font-weight: bold;">
							<a href="<?php print urldecode(caEditorUrl($this->request, 'ca_objects', '{object_id}')); ?>" class="caEditItemButton" id="<?php print $vs_id_prefix; ?>_edit_related_{n}">{name} ({idno})</a>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php print $t_order_item->htmlFormElement('service', null, array('value' => '{service}', 'name' => $vs_id_prefix.'_service_{n}', 'id' => $vs_id_prefix.'_service_{n}')); ?></td>
					<td><?php print $t_order_item->htmlFormElement('fullfillment_method',  null, array('value' => '{fullfillment_method}', 'name' => $vs_id_prefix.'_fullfillment_method_{n}', 'id' => $vs_id_prefix.'_fullfillment_method_{n}')); ?></td>
					<td rowspan='3' valign='top' width='125'>
						<a href='#' onclick='caMediaPanel.showPanel("<?php print urldecode(caNavUrl($this->request, 'client', 'OrderEditor', 'SelectRepresentations', array('object_id' => "{object_id}", 'item_id' => "{item_id}"))); ?>", function() { jQuery("#orderItemRepresentationSelect{n}").load("<?php print urldecode(caNavUrl($this->request, 'client', 'OrderEditor', 'GetSelectedRepresentationCount', array('item_id' => '{item_id}'))); ?>"); }); return false;' >{thumbnail_tag}</a>
						<br/>
						<span id="orderItemRepresentationSelect{n}"><a href='#' onclick='caMediaPanel.showPanel("<?php print urldecode(caNavUrl($this->request, 'client', 'OrderEditor', 'SelectRepresentations', array('object_id' => "{object_id}", 'item_id' => "{item_id}"))); ?>", function() { jQuery("#orderItemRepresentationSelect{n}").load("<?php print urldecode(caNavUrl($this->request, 'client', 'OrderEditor', 'GetSelectedRepresentationCount', array('item_id' => '{item_id}'))); ?>"); }); return false;' ><?php print _t('%1/%2 pages selected', "{selected_representation_count}", "{representation_count}"); ?></a></span>
					</td>
				</tr>
				<tr>
					<td><?php print $t_order_item->htmlFormElement('fee', $vs_currency_input_format, array('classname' => 'currencyBg', 'value' => '{fee}', 'name' => $vs_id_prefix.'_fee_{n}', 'id' => $vs_id_prefix.'_fee_{n}')); ?></td>
					<td><?php print $t_order_item->htmlFormElement('tax',  $vs_currency_input_format, array('classname' => 'currencyBg', 'value' => '{tax}', 'name' => $vs_id_prefix.'_tax_{n}', 'id' => $vs_id_prefix.'_tax_{n}')); ?></td>
				</tr>
				<tr>
					<td width='260'><?php print str_replace('textarea', 'textentry', $t_order_item->htmlFormElement('notes', null, array('value' => '{notes}', 'name' => $vs_id_prefix.'_notes_{n}', 'id' => $vs_id_prefix.'_notes_{n}', 'width' => '250px'))); ?></td>
					<td width='330'><?php print str_replace('textarea', 'textentry', $t_order_item->htmlFormElement('restrictions',  null, array('value' => '{restrictions}', 'name' => $vs_id_prefix.'_restrictions_{n}', 'id' => $vs_id_prefix.'_restrictions_{n}', 'width' => '250px'))); ?></td>
				</tr>
<?php
	if ($vs_additional_fees = $this->getVar('additional_fees')) {
?>
				<tr>
					<td colspan="2">
						<?php print $vs_additional_fees; ?>
					</td>
				</tr>
<?php
	}
?>
			</table>
			
			<input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
			
		</div>
	</textarea>
<?php
	//
	// Template to generate controls for creating new relationship
	//
?>
	<textarea class='caNewItemTemplate' style='display: none;'>
		<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo">
			<a href="#" class="caDeleteItemButton" style="float: right;"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__); ?></a>
			
			<table class="caListItem">
				<tr>
					<td colspan='2'>
						<div class="formLabel"><?php print _t('Object'); ?>
							<input type="text" size="100" name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" class="lookupBg"/>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php print $t_order_item->htmlFormElement('service', null, array('value' => '{service}', 'name' => $vs_id_prefix.'_service_{n}', 'id' => $vs_id_prefix.'_service_{n}', 'onchange' => 'caGetDefaultFee("'.$vs_id_prefix.'_service_{n}", "'.$vs_id_prefix.'_fee_{n}", "{n}")')); ?></td>
					<td><?php print $t_order_item->htmlFormElement('fullfillment_method',  null, array('value' => '{fullfillment_method}', 'name' => $vs_id_prefix.'_fullfillment_method_{n}', 'id' => $vs_id_prefix.'_fullfillment_method_{n}')); ?></td>
				</tr>
				<tr>
					<td><?php print $t_order_item->htmlFormElement('fee', $vs_currency_input_format, array('classname' => 'currencyBg', 'value' => '{fee}', 'name' => $vs_id_prefix.'_fee_{n}', 'id' => $vs_id_prefix.'_fee_{n}')); ?></td>
					<td><?php print $t_order_item->htmlFormElement('tax',  $vs_currency_input_format, array('classname' => 'currencyBg', 'value' => '{tax}', 'name' => $vs_id_prefix.'_tax_{n}', 'id' => $vs_id_prefix.'_tax_{n}')); ?></td>
				</tr>
				<tr>
					<td><?php print str_replace('textarea', 'textentry', $t_order_item->htmlFormElement('notes', null, array('value' => '{notes}', 'name' => $vs_id_prefix.'_notes_{n}', 'id' => $vs_id_prefix.'_notes_{n}'))); ?></td>
					<td><?php print str_replace('textarea', 'textentry', $t_order_item->htmlFormElement('restrictions',  null, array('value' => '{restrictions}', 'name' => $vs_id_prefix.'_restrictions_{n}', 'id' => $vs_id_prefix.'_restrictions_{n}'))); ?></td>
				</tr>
<?php
	if ($vs_additional_fees = $this->getVar('additional_fees_for_new_items')) {
?>
				<tr>
					<td colspan="2">
						<?php print $vs_additional_fees; ?>
					</td>
				</tr>
<?php
	}
?>
			</table>
			<input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
		</div>
		<script type="text/javascript"><?php
			print 'caGetDefaultFee("'.$vs_id_prefix.'_service_{n}", "'.$vs_id_prefix.'_fee_{n}", "{n}")';
		?></script>
	</textarea>
	
	<div class="bundleContainer">
		<div class="caItemList">
		
		</div>
		<input type="hidden" name="<?php print $vs_id_prefix; ?>BundleList" id="<?php print $vs_id_prefix; ?>BundleList" value=""/>
		<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
		<div class='button labelInfo caAddItemButton'><a href='#'><?php print caNavIcon($this->request, __CA_NAV_BUTTON_ADD__); ?> <?php print _t("Add item to order"); ?></a></div>
	</div>
</div>
<?php


	print $t_order->htmlFormElement('order_id');

?>
</form>
<br/>
<?php
	print $vs_control_box;
?>			
<script type="text/javascript">
	var caRelationBundle<?php print $vs_id_prefix; ?>;
	jQuery(document).ready(function() {
		caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix.'_item'; ?>', {
			fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
			templateValues: ['_display', 'id', 'object_id', 'item_id', 'name', 'name_sort', 'idno', 'idno_sort', 'service', 'fullfillment_method', 'fee', 'tax', 'notes', 'restrictions', 'thumbnail_tag', 'representation_count'<?php print (sizeof($va_additional_fee_template_codes)) ? ", ".join(", ", $va_additional_fee_template_codes) : ""; ?>],
			initialValues: <?php print json_encode($va_initial_values); ?>,
			itemID: '<?php print $vs_id_prefix; ?>Item_',
			templateClassName: 'caNewItemTemplate',
			initialValueTemplateClassName: 'caItemTemplate',
			itemListClassName: 'caItemList',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			showEmptyFormsOnLoad: 1,
			autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'Object', 'Get', $va_lookup_params); ?>',
			isSortable: true,
			listSortOrderID: '<?php print $vs_id_prefix; ?>BundleList',
			listSortItems: 'div.sortableOrderItem'
		});
	});
	
	var caDefaultFees = <?php print json_encode($this->getVar('default_item_prices')); ?>;
	function caGetDefaultFee(serviceDOMID, feeDOMID, n) {
			var v= jQuery("#" + serviceDOMID).val();
			
			var value;
			if (!(value = caDefaultFees[v])) { value = caDefaultFees['__default__']['base']; }
			jQuery("#" + feeDOMID).val(caDefaultFees[v]['base']);
	}
	
</script>