<?php
if (!defined('ABSPATH')) exit;
function mis360_registration_locations() {
 static $locations=null;
 if($locations===null)$locations=json_decode(file_get_contents(get_template_directory().'/assets/data/turkiye-locations.json'),true) ?: array();
 return $locations;
}
function mis360_registration_location_valid($city,$district) {
 $locations=mis360_registration_locations();return isset($locations[$city])&&in_array($district,$locations[$city],true);
}
function mis360_registration_location_select($type) {
 $locations=mis360_registration_locations();
 $city=isset($_POST['city'])&&is_string($_POST['city'])?sanitize_text_field(wp_unslash($_POST['city'])):'';
 $district=isset($_POST['district'])&&is_string($_POST['district'])?sanitize_text_field(wp_unslash($_POST['district'])):'';
 if($type==='city'){?>
 <select name="city" id="city" required class="yym-input" data-registration-locations="<?php echo esc_attr(wp_json_encode($locations,JSON_UNESCAPED_UNICODE)); ?>"><option value="">İl seçin</option><?php foreach($locations as $name=>$items): ?><option value="<?php echo esc_attr($name); ?>" <?php selected($city,$name); ?>><?php echo esc_html($name); ?></option><?php endforeach; ?></select>
 <?php }else{ ?>
 <select name="district" id="district" required class="yym-input" <?php disabled(!isset($locations[$city])); ?>><option value=""><?php echo isset($locations[$city])?'İlçe seçin':'Önce il seçin'; ?></option><?php foreach($locations[$city]??array() as $name): ?><option value="<?php echo esc_attr($name); ?>" <?php selected($district,$name); ?>><?php echo esc_html($name); ?></option><?php endforeach; ?></select>
 <?php }
}
