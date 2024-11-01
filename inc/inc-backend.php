<?php

/**
 * Register a custom menu page.
 */

add_action('admin_menu', 'spcfw_registerCouponOptionsPage', 9999);
function spcfw_registerCouponOptionsPage() {
    add_submenu_page( 
        'woocommerce',
        __('Simple PDF Coupons', 'simple-pdf-coupon-for-woocommerce'),
        __('Simple PDF Coupons', 'simple-pdf-coupon-for-woocommerce'), 
        'edit_products', 
        'spcfwCoupon', 
        'spcfw_optionsPage', 
        9999 
    );
}

add_action('admin_init', 'spcfw_registerSettings');
function spcfw_registerSettings() {
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_suject', 'string');
    
    add_option( 'lemontec_pdf_coupon_maxvalue', 100);
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_maxvalue', 'number');
    
    add_option( 'lemontec_pdf_coupon_infotext', __('Thank you for your order', 'lemontec-coupon-for-woocommerce'));
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_infotext', 'string');
   
    add_option( 'lemontec_pdf_coupon_btn_text', __('Link', 'lemontec-coupon-for-woocommerce'));
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_btn_text', 'string');
   
    add_option( 'lemontec_pdf_coupon_pos_top', 150);
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_pos_top', 'number');
    
    add_option( 'lemontec_pdf_coupon_position', 'center');
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_position', 'string');
    
    add_option( 'lemontec_pdf_coupon_fontsize_price', 50);
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_fontsize_price', 'number');
    
    add_option( 'lemontec_pdf_coupon_fontsize_greeting', 15);
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_fontsize_greeting', 'number');
    
    add_option( 'lemontec_pdf_coupon_fontsize_couponnumber', 12);
    register_setting('spcfw_OptionsGroup', 'lemontec_pdf_coupon_fontsize_couponnumber', 'number');
    
   
}

function spcfw_optionsPage() { ?>
    <div class="wrap">	
        <a href="https://lemontec.at" target="_blank" style="max-width: 200px; display: block; margin: 20px 0 0;"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>img/lemontec-logo.svg" alt="LEMONTEC WEBAGENTUR LOGO"></a>
        
        <h1><?php esc_html_e('Simple Coupons for WooCommerce Settings' , 'simple-pdf-coupon-for-woocommerce'); ?></h1>
        <div style="background-color:#fff; padding:10px; margin: 15px 0;">   
            <?php esc_html_e('If you have any questions, write to: office@lemontec.at', 'simple-pdf-coupon-for-woocommerce'); ?>            
        </div>
    
        <h2><?php esc_html_e('Coupon settings' , 'simple-pdf-coupon-for-woocommerce'); ?></h2>
    
        <form method="post" action="options.php">
            <?php settings_fields('spcfw_OptionsGroup'); ?>
            <table>
                <tr>
                    <td><label for="lemontec_pdf_coupon_suject"><?php esc_html_e('URL to Coupon Sujet' , 'simple-pdf-coupon-for-woocommerce'); ?> (Format: A4) <br></label></td>
                    <td><input type="text" id="lemontec_pdf_coupon_suject" name="lemontec_pdf_coupon_suject" value="<?php echo get_option('lemontec_pdf_coupon_suject'); ?>"></td>
                </tr>
                <tr>
                    <td><label for="lemontec_pdf_coupon_maxvalue"><?php esc_html_e('Maximum coupon value' , 'simple-pdf-coupon-for-woocommerce'); ?></label></td>
                    <td><input type="number" name="lemontec_pdf_coupon_maxvalue" value="<?php echo get_option('lemontec_pdf_coupon_maxvalue'); ?>" id="lemontec_pdf_coupon_maxvalue"> €</td>
                </tr>
                <tr>
                    <td><label for="lemontec_pdf_coupon_infotext"><?php esc_html_e('Text in mail and on thankyou page' , 'simple-pdf-coupon-for-woocommerce'); ?></label></td>
                    <td><textarea id="lemontec_pdf_coupon_infotext" name="lemontec_pdf_coupon_infotext" placeholder="<?php esc_html_e('Thank you for your order.' , 'simple-pdf-coupon-for-woocommerce'); ?>"><?php echo get_option('lemontec_pdf_coupon_infotext'); ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="lemontec_pdf_coupon_btn_text"><?php esc_html_e('Button text in mail and on thankyou page' , 'simple-pdf-coupon-for-woocommerce'); ?></label></td>
                        <td><input type="text" name="lemontec_pdf_coupon_btn_text" value="<?php echo get_option('lemontec_pdf_coupon_btn_text'); ?>" placeholder="<?php esc_html_e('Here is the link to your voucher.' , 'simple-pdf-coupon-for-woocommerce'); ?>" id="lemontec_pdf_coupon_btn_text"></td>
                    </tr>
            </table>
            
            <h3><?php esc_html_e('Container for price and coupon number' , 'simple-pdf-coupon-for-woocommerce'); ?></h3>
            <table>
                <tr>
                    <td><label for="lemontec_pdf_coupon_pos_top"><?php esc_html_e('Top' , 'simple-pdf-coupon-for-woocommerce'); ?></label></td>
                    <td><input type="number" name="lemontec_pdf_coupon_pos_top" value="<?php echo get_option('lemontec_pdf_coupon_pos_top'); ?>" id="lemontec_pdf_coupon_pos_top"> mm</td>
                </tr>
                <tr>
                    <td><label for="lemontec_pdf_coupon_position"><?php esc_html_e('Position horizontal' , 'simple-pdf-coupon-for-woocommerce'); ?> (left, center, right)</label></td>
                    <td>
                        <input type="text" name="lemontec_pdf_coupon_position" value="<?php echo get_option('lemontec_pdf_coupon_position'); ?>" id="lemontec_pdf_coupon_position">
                    </td>
                </tr>
                <tr>
                    <td><label for="lemontec_pdf_coupon_fontsize_price"><?php esc_html_e('Font Size Price' , 'simple-pdf-coupon-for-woocommerce'); ?> </label></td>
                    <td>
                        <input type="text" name="lemontec_pdf_coupon_fontsize_price" value="<?php echo get_option('lemontec_pdf_coupon_fontsize_price'); ?>" id="lemontec_pdf_coupon_fontsize_price"> pt
                    </td>
                </tr>
                <tr>
                    <td><label for="lemontec_pdf_coupon_fontsize_greeting"><?php esc_html_e('Font Size Greeting' , 'simple-pdf-coupon-for-woocommerce'); ?></label></td>
                    <td>
                        <input type="text" name="lemontec_pdf_coupon_fontsize_greeting" value="<?php echo get_option('lemontec_pdf_coupon_fontsize_greeting'); ?>" id="lemontec_pdf_coupon_fontsize_greeting"> pt
                    </td>
                </tr>              
                <tr>
                    <td><label for="lemontec_pdf_coupon_fontsize_couponnumber"><?php esc_html_e('Font Size Coupon Number' , 'simple-pdf-coupon-for-woocommerce'); ?></label></td>
                    <td>
                        <input type="number" name="lemontec_pdf_coupon_fontsize_couponnumber" value="<?php echo get_option('lemontec_pdf_coupon_fontsize_couponnumber'); ?>" id="lemontec_pdf_coupon_fontsize_couponnumber"> pt
                    </td>
                </tr>
            </table>  
            
            <?php submit_button(); ?>
        </form>
  </div>
<?php
}