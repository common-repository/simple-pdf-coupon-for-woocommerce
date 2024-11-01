<?php
/**
 * Plugin Name: Simple PDF Coupon for Woocommerce
 * Plugin URI: https://lemontec.at
 * Description: WooCommerce Simple PDF Coupon module to create PDF coupons
 * Version: 1.0.0
 * Author: LEMONTEC
 * Author URI: https://lemontec.at
 * License: GPL2
 * Domain Path: /languages
 Text Domain: simple-pdf-coupon-for-woocommerce

 * WC requires at least: 4.6
 * WC tested up to: 5.7
 */

add_action('init', 'spcfw_init');
function spcfw_init() {
  load_plugin_textdomain( 'simple-pdf-coupon-for-woocommerce', false, 'simple-pdf-coupon-for-woocommerce/languages' );
}

require_once('inc/inc-backend.php');

add_action('wp_enqueue_scripts', 'spcfw_loadScripts' );
/**
* Include scripts and styles
*/ 
function spcfw_loadScripts() {
    $plugin_js = plugins_url( 'js/lemontec_coupon_scripts.js', __FILE__ ); //-->name
    wp_enqueue_script( 'spcfw_scripts', $plugin_js, '', '2.6.7', true );
    
    $plugin_style = plugins_url( 'css/lemontec_coupon_styles.css', __FILE__ );
    wp_enqueue_style('spcfw_styles', $plugin_style, array(), '1.0', 'all'); //->name
}


add_filter('product_type_selector', 'spcfw_addCustomProductType');

/**
* Add New Product Type to Select Dropdown
*
* @param array $types
* @returns $types
*/ 
function spcfw_addCustomProductType($types){
    $types['lemontec_gutschein'] = 'Simple PDF Coupon';
    return $types;
}
 
/* --------------------------
**  Add New Product Type Class
*/

add_action('init', 'spcfw_createProductType');
/**
* Create new product type Simple PDF Coupon
*
* @returns product type
*/ 
function spcfw_createProductType(){
    class WC_Product_Gutschein extends WC_Product {
      public function get_type() {
         return 'lemontec_gutschein';
      }
    }
}

add_filter('woocommerce_product_class', 'spcfw_wooProductClass', 10, 2);
/**
* Load new product type Simple PDF Coupon class
*
* @param string $classname
* @param string $product_type
* @returns $classname
*/ 
function spcfw_wooProductClass($classname, $product_type) {
    if ( $product_type == 'lemontec_gutschein' ) {
        $classname = 'WC_Product_Gutschein';
    }
    return $classname;
}

add_filter('woocommerce_product_data_tabs', 'spcfw_addTab');
/**
* Add tab for type Simple PDF Coupon in product detail page
*
* @param array $tabs
* @returns $tabs
*/ 
function spcfw_addTab($tabs) {
	$tabs['lemontec_gutschein'] = array(
		'label'	 => 'Gutschein',
		'target' => 'lemontec_gutschein_options',
		'class'  => array('show_if_lemontec_gutschein'),
	);
	return $tabs;
}

add_action('admin_footer', 'spcfw_singleProductJs');
function spcfw_singleProductJs() {
    if ('product' != get_post_type()) :
        return;
    endif;
    ?>
    <script type='text/javascript'>
        jQuery(document).ready(function () {
            jQuery('.product_data_tabs .general_tab').addClass('show_if_lemontec_gutschein').show();
            jQuery('#general_product_data .pricing').addClass('show_if_lemontec_gutschein').show();
            jQuery('.inventory_options').addClass('show_if_lemontec_gutschein').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_lemontec_gutschein').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_lemontec_gutschein').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_lemontec_gutschein').show();
        });
    </script>
    <?php
}

add_action('woocommerce_lemontec_gutschein_add_to_cart', function() {
    do_action('woocommerce_simple_add_to_cart');
});


/**
* Add textarea for personal greeting and slider for coupon value to new product type Simple PDF Coupon above the add-to-cart-button on product single page
*
*/ 

add_action('woocommerce_before_add_to_cart_button', 'spcfw_addInputFieldsToProduct', 9 );
function spcfw_addInputFieldsToProduct() {    
    global $product;
        
    if($product->get_type() == 'lemontec_gutschein') { ?>
        <div class="lemontec_slidecontainer">
            <input type="range" min="10" max="100" value="50" step="10" id="lemontec_coupon_slider" name="lemontec_coupon_slider_value">
            <p><?php _e("Value" , "simple-pdf-coupon-for-woocommerce"); ?>: <span id="lemontec_coupon_slider_value"></span></p>
        </div>

        <?php $value = isset($_POST['lemontec_coupon_greeting']) ? sanitize_textarea_field($_POST['lemontec_coupon_greeting']) : ''; ?>

        <div>
            <label><?php _e("Personal greeting" , "simple-pdf-coupon-for-woocommerce"); ?></label>
            <p><textarea style="width:100%;" rows="4" name="lemontec_coupon_greeting" placeholder="<?php echo $value; ?>"><?php echo $value; ?></textarea></p>
        </div>
    <?php } 
}


add_filter('woocommerce_add_cart_item_data', 'spcfw_addCartItemData', 25, 2);
/**
* Save values to cart item data
*
* @param array $cart_item_meta
* @param int $product_id
* @returns $cart_item_meta
*/ 
function spcfw_addCartItemData($cart_item_meta, $product_id) {
    $custom_data  = array() ;
    $custom_data['lemontec_coupon_greeting'] = isset($_POST['lemontec_coupon_greeting']) ? sanitize_textarea_field(/*nl2br(*/$_POST['lemontec_coupon_greeting'])/*) */: "" ; //-->sanitize
    $custom_data['lemontec_coupon_slider_value'] = isset($_POST['lemontec_coupon_slider_value']) ? sanitize_text_field ($_POST ['lemontec_coupon_slider_value']) : "" ;
    $cart_item_meta['custom_data'] = $custom_data ; 
	return $cart_item_meta;
}


add_action('woocommerce_before_calculate_totals', 'spcfw_changeProductPrice', 11);
/**
* Change price of Simple PDF Coupon depending on user input
*
* @param array $cart_object
*/ 
function spcfw_changeProductPrice($cart_object) {
    foreach ( $cart_object->cart_contents as $key => $value ) {
        if($value['custom_data']['lemontec_coupon_slider_value']) {
            $calc_price = $value['custom_data']['lemontec_coupon_slider_value'];
            $value['data']->set_price($calc_price);    
        }
    }
}

// dislay greeting in cart
add_filter('woocommerce_get_item_data', 'spcfw_getItemData', 25, 2);
/**
* Change price of Simple PDF Coupon depending on user input
*
* @param array $other_data
* @param array $cart_item
* @returns $other_data
*/ 
function spcfw_getItemData ($other_data, $cart_item) {
	if (isset($cart_item['custom_data'])) {
		$custom_data  = $cart_item['custom_data'];
        if($custom_data['lemontec_coupon_greeting'] != '') $other_data[] = array('name' => 'Text', 'display'  => $custom_data['lemontec_coupon_greeting']);          
    }
    
	return $other_data;
}

//display greeting in checkout and mails
add_action( 'woocommerce_checkout_create_order_line_item', 'spcfw_addOrderItemMeta', 10, 4);  

/**
* Display greeting and price in checkout and mails,
* Change price of Simple PDF Coupon depending on user input
*
* @param array $item
* @param array $cart_item_key
* @param array $values
*/ 
function spcfw_addOrderItemMeta($item, $cart_item_key, $values, $orders) {    
    if (isset($values['custom_data'])) {
        $custom_data  = $values ['custom_data'];
        
        if($custom_data['lemontec_coupon_slider_value'] != '') {
            $coupon_codes = '';
            $quantity = $values['quantity'];
            for($i = 1; $i <= $quantity; $i++) {
                //create coupon code
                $characters = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
                $char_length = "8";
                $coupon_code = substr(str_shuffle($characters ),  0, $char_length );
            
                $coupon_codes .= $coupon_code . ' ';

                $amount = $custom_data['lemontec_coupon_slider_value']; // Amount
                $discount_type = 'fixed_cart';

                $coupon = array(
                    'post_title' => $coupon_code,
                    'post_content' => $custom_data['lemontec_coupon_greeting'],
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type'		=> 'shop_coupon'
                );

                $new_coupon_id = wp_insert_post( $coupon );

                // Add meta of coupon
                update_post_meta($new_coupon_id, 'description', $custom_data['lemontec_coupon_greeting']);
                update_post_meta($new_coupon_id, 'discount_type', $discount_type);
                update_post_meta($new_coupon_id, 'coupon_amount', $amount);
                update_post_meta($new_coupon_id, 'individual_use', 'no');
                update_post_meta($new_coupon_id, 'product_ids', '' );
                update_post_meta($new_coupon_id, 'exclude_product_ids', '');
                update_post_meta($new_coupon_id, 'usage_limit', '1');
                update_post_meta($new_coupon_id, 'expiry_date', '');
                update_post_meta($new_coupon_id, 'apply_before_tax', 'yes' );
                update_post_meta($new_coupon_id, 'free_shipping', 'no');
            }
        
            $item->update_meta_data('Nr', $coupon_codes); 
            if($custom_data['lemontec_coupon_greeting'] != '') $item->update_meta_data('Text', $custom_data['lemontec_coupon_greeting']);  
        }
    }
}

add_action( 'woocommerce_thankyou', 'spcfw_addTextToThankyouPage', 10,1);
/**
* Add Infotext and Button to Thank You Page
*
* @param int $order_id
*/ 
function spcfw_addTextToThankyouPage($order_id) {
    $btn_txt = get_option('lemontec_pdf_coupon_btn_text');
    $info_txt = get_option('lemontec_pdf_coupon_infotext');

    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product = $item->get_product();

        if($product->is_type('lemontec_gutschein')) {
            $link = home_url() . '/?coupon_pdf&order=' . $order_id.'&orderkey='.$order->get_order_key();
            echo '<p>' . $info_txt . ' <a class="button" href="'.$link.'" target="_blank">'.$btn_txt.'</a></p>';
            break;
        } 
    }
}

/*
* Add Infotext and Button to Mails
*/

add_action('woocommerce_email_before_order_table', 'spcfw_addTextToMail', 20, 4);
/**
* Add Infotext and Button to Mails
*
* @param object $order
* @param object $email
*/ 
function spcfw_addTextToMail( $order, $sent_to_admin, $plain_text, $email ) {
    $btn_txt = get_option('lemontec_pdf_coupon_btn_text');
    $info_txt = get_option('lemontec_pdf_coupon_infotext');
    
    $order_id = $order->get_id();
    if ($email->id == 'customer_processing_order' || $email->id == 'customer_on_hold_order') {
        foreach ($order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            $product = $item->get_product();

            if($product->is_type('lemontec_gutschein')) {
                $link = home_url() . '/?coupon_pdf&order=' . $order_id.'&orderkey='.$order->get_order_key();
                echo '<p>' . $info_txt . ' <a href="'.$link.'" target="_blank">'.$btn_txt.'</a></p>';
                break;
            } 
        }
   }
}

add_filter('woocommerce_product_add_to_cart_text', 'spcfw_changeAddToCartBtnText',10,1); 
/**
* Change Add To Cart Button on Archive Page
*
* @param string $text
* @return $text
*/ 
function spcfw_changeAddToCartBtnText($text) {
    global $product;
    if($product->get_type() == 'lemontec_gutschein') {
        return __( 'Add to cart', 'woocommerce' );
    } else {
        return $text;
    }
}

add_filter( 'template_include', function($template) {
    return isset($_GET['coupon_pdf']) ? include('inc/coupon_pdf.php') : $template ;
}, 99 );





