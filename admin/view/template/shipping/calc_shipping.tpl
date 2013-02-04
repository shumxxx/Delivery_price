<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">

      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?php echo $entry_tax_class; ?></td>
            <td><select name="calc_shipping_tax_class_id">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($tax_classes as $tax_class) { ?>
                  <?php if ($tax_class['tax_class_id'] == $calc_shipping_tax_class_id) { ?>
                  <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="calc_shipping_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $calc_shipping_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="calc_shipping_status">
                <?php if ($calc_shipping_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="calc_shipping_sort_order" value="<?php echo $calc_shipping_sort_order; ?>" size="1" /></td>
          </tr>

          <tr>
            <td colspan="2">
              <table width="100%" id="new_store">
                <tr><th colspan="6">Add new store</th></tr>
                <tr><th>Country</th><th>City</th><th>Adress</th><th>Free distance</th><th><?php echo $entry_cost; ?></th><th>Active</th><th></th></tr>
                 <tr>
                   <td align="center">
                    <input type="text" id="calc_country" name="calc_country" value="" />
                   <!-- 
                     <select name="country_id" class="large-field">
                        <option value=""><?php echo $text_select; ?></option>
                          <?php foreach ($countries as $country) { ?>
                            <?php if ($country['country_id'] == $country_id) { ?>
                              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                     </select>-->
                   
                   </td>
                   <td align="center">
                   <input type="text" id="calc_city" name="calc_city" value="" />
                  <!--   <select name="zone_id" class="large-field">
                       <option value=""></option>
                      </select>-->
                   </td>
                   <td align="center"><input type="text" id="calc_adress" name="calc_adress" value="" /></td>
                   <td align="center"><input type="text" id="calc_free_distance" name="calc_free_distance" value="" /></td>
                   <td align="center"><input type="text" id="calc_price_per_one" name="calc_price_per_one" value="" /></td>
                   <td align="center"><input type="checkbox" id="calc_active" name="calc_active" checked/></td>
                   <td align="center"><a href="#" onClick="top.AddDeliverySetting();$(this).fadeTo(250, 0.2);$(this).fadeTo(150, 0.7);return false;">Add</a></td>
                 <tr>
                 <tr><th id="info" align="center" colspan="7"></th></tr>
              </table>

            </td>
          </tr> 
          <tr>
            <td colspan="2">
            <strong>IF Distance < Free distance Distance price = 0</strong><br />
            <strong>Mode#1 - caculate Distance price = (Distance - Free distance) * Price per one</strong><br />
            <strong>Mode#2 - caculate Distance price = Distance * Price per one</strong><br />
            </td>
          </tr>                   
          <tr>
            <td colspan="2">
            
              <table width="100%" id="store_list">
                <tr><th colspan="8">Store list</th></tr>
                <tr><th>#</th><th>Mode</th><th>Country</th><th>City</th><th>Adress</th><th>Free distance</th><th><?php echo $entry_cost; ?></th><th>Sort</th><th>Active</th><th>Action</th></tr>
              <?php 
                 $i = 1;
                 foreach ($delivery_list as $key=>$value) { ?>
                 <tr>
                   <td align="center"><?php echo $i.".";?></td>
                   <td align="center">
                     <?php
                       $first_selected = '';
                       $second_selected = '';
                       if ($delivery_list[$key]['mode'] == 1) $first_selected = 'selected';
                       elseif ($delivery_list[$key]['mode'] == 2) $second_selected = 'selected';
                       
                       $select = '<select id="calc_mode'.$key.'" name="calc_mode'.$key.'">';
                       $select .= '<option value="1" '.$first_selected.'>1</option>';
                       $select .= '<option value="2" '.$second_selected.'>2</option>';
                       $select .= '</select>';
                       
                       echo $select;
                     
                     ?>
                   
                   </td>
                   <td align="center"><input type="text" id="calc_country<?php echo $key;?>" name="calc_country<?php echo $key;?>" value="<?php echo $delivery_list[$key]['country'];?>" /></td>
                   <td align="center"><input type="text" id="calc_city<?php echo $key;?>" name="calc_city<?php echo $key;?>" value="<?php echo $delivery_list[$key]['city'];?>" /></td>
                   <td align="center"><input type="text" id="calc_adress<?php echo $key;?>" name="calc_adress<?php echo $key;?>" value="<?php echo $delivery_list[$key]['adress'];?>" /></td>
                   <td align="center"><input type="text" id="calc_free_distance<?php echo $key;?>" name="calc_free_distance<?php echo $key;?>" value="<?php echo $delivery_list[$key]['free_distance'];?>" /></td>
                   <td align="center"><input type="text" id="calc_price_per_one<?php echo $key;?>" name="calc_price_per_one<?php echo $key;?>" value="<?php echo $delivery_list[$key]['price_per_one'];?>" /></td>
                   <td align="center"><input type="text" size="1" id="calc_sort<?php echo $key;?>" name="calc_sort<?php echo $key;?>" value="<?php echo $delivery_list[$key]['sort'];?>" /></td>
                   <td align="center"><input type="checkbox" id="calc_active<?php echo $key;?>" name="calc_active<?php echo $key;?>" <?php if ($delivery_list[$key]['active'] == 1) echo 'checked';?> value='1'/></td>
                   <td align="center"><a href="#" onClick="top.SaveDeliverySettings(<?php echo $key;?>);$(this).fadeTo(250, 0.2);$(this).fadeTo(150, 0.7);return false;">Save</a></td>
                 <tr>
              <?php $i++; } ?>
              </table>
              <input type="hidden" id="rows_count" name="rows_count" value="<?php echo ($i-1);?>" />
              <input type="hidden" id="token" name="token" value="<?php echo $token;?>" />
            </td>
          </tr>          
        </table>
        
      </form>
    </div>
  </div>
</div>

<script type="text/javascript"><!--

function SaveDeliverySettings(delivery_id) {
  var calc_country = $('#calc_country' + delivery_id).val();
  var calc_city = $('#calc_city' + delivery_id).val();
  var calc_adress = $('#calc_adress' + delivery_id).val();
  var calc_free_distance = $('#calc_free_distance' + delivery_id).val();
  var calc_price_per_one = $('#calc_price_per_one' + delivery_id).val();
  var calc_active = $('#calc_active' + delivery_id).val();
  var calc_sort = $('#calc_sort' + delivery_id).val();
  var calc_mode = $('#calc_mode' + delivery_id).val();
  var token = $('#token').val();

  $.post('index.php?route=shipping/calc_shipping/save_delivery_settings&token='+ token, 
         '&delivery_id=' + delivery_id + 
         '&country=' + calc_country + 
         '&city=' + calc_city +
         '&adress=' + calc_adress +
         '&free_distance=' + calc_free_distance +
         '&price_per_one=' + calc_price_per_one +
         '&sort=' + calc_sort +
         '&mode=' + calc_mode +
         '&active=' + calc_active);
  
}
function AddDeliverySetting() {
  var calc_country = $('#calc_country').val();
  var calc_city = $('#calc_city').val();
  var calc_adress = $('#calc_adress').val();
  var calc_free_distance = $('#calc_free_distance').val();
  var calc_price_per_one = $('#calc_price_per_one').val();
  var calc_active = $('#calc_active').val();
  var token = $('#token').val();
  var rows_count = $('#rows_count').val();
  
  $.ajax({
		url: 'index.php?route=shipping/calc_shipping/add_delivery_setting&token='+ token,
		dataType: 'json',
        type: 'POST',	
        data: ({country : calc_country, city : calc_city, adress : calc_adress, free_distance:calc_free_distance, price_per_one:calc_price_per_one, active:calc_active}),		
		success: function(json) {
          html = "";   
          if (json['status'] == 'OK'){
            html += '<font color="green"><b>' + json['message'] + '</b></font>';
            active = "";
            if (json['calc_active'] == 'on') active = "checked";
            rows_count = parseInt(rows_count) + 1;

            $('#store_list tr:last').after('<tr>'
                   +'<td align="center">' + rows_count + '</td><td align="center"><input type="text" id="calc_country' + json['store_id'] + '" name="calc_country' + json['store_id'] + '" value="' + json['country'] + '" /></td>'
                   +'<td align="center"><input type="text" id="calc_city' + json['store_id'] + '" name="calc_city' + json['store_id'] + '" value="' + json['city'] + '" /></td>'
                   +'<td align="center"><input type="text" id="calc_adress' + json['store_id'] + '" name="calc_adress' + json['store_id'] + '" value="' + json['adress'] + '" /></td>'
                   +'<td align="center"><input type="text" id="calc_free_distance' + json['store_id'] + '" name="calc_free_distance' + json['store_id'] + '" value="' + json['free_distance'] + '" /></td>'
                   +'<td align="center"><input type="text" id="calc_price_per_one' + json['store_id'] + '" name="calc_price_per_one' + json['store_id'] + '" value="' + json['price_per_one'] + '" /></td>'
                   +'<td align="center"><input type="checkbox" id="calc_active' + json['store_id'] + '" name="calc_active' + json['store_id'] + '" ' + active + ' value="1"/></td>'
                   +'<td align="center"><a href="#" onClick="top.SaveDeliverySettings(' + json['store_id'] + ');$(this).fadeTo(250, 0.2);$(this).fadeTo(150, 0.7);return false;">Save</a></td>'
                   +'<tr>');
            $('#calc_country').val('');
            $('#calc_city').val('');
            $('#calc_adress').val('');
            $('#calc_free_distance').val(0);
            $('#calc_price_per_one').val(0);
            $('#rows_count').val(rows_count);
    
          } else {
            html += '<font color="red"><b>' + json['message'] + '</b></font>';    
          }



		  $('#info').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
  });
}

//--></script>

<script type="text/javascript"><!--
$('#new_store select[name=\'country_id\']').bind('change', function() {
    alert('this.value:'+this.value);
    var token = $('#token').val();
	$.ajax({
		url: 'index.php?route=shipping/calc_shipping/zone&token='+ token+'&country_id='+this.value,
		dataType: 'json',
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
	
			html = '';
			
			if (json['zone'].length > 0) {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '">';
	    			html += json['zone'][i]['name'] +'</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('#new_store select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#new_store select[id=\'country_id\']').trigger('change');
//--></script>

<?php echo $footer; ?> 