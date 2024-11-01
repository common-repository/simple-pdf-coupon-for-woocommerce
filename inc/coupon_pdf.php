<?php
use Dompdf\Dompdf;
use Dompdf\Options;

$class_dir = trailingslashit( dirname( plugin_basename( __FILE__ ) ) );
$plugin_name = explode('/',$class_dir);

$autoload_link = WP_PLUGIN_DIR . '/' . $plugin_name[0] . '/vendor/autoload.php';

include_once($autoload_link);

function spcfw_displayHtmlAsPdf($output){
    $options = new Options();
    $options->setIsHtml5ParserEnabled(false);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($output,'UTF-8');
    $dompdf->setPaper('A4','portrait');
    $options->set('isRemoteEnabled',true);    
    $dompdf->render();
    
    ob_end_clean();
    $dompdf->stream("MeinGutschein",array("Attachment"=>true));
}

global $woocommerce;
global $post;



$order_id = isset($_GET['order']) ? filter_var($_GET['order'], FILTER_SANITIZE_NUMBER_INT) : "";
$order_key = isset($_GET['orderkey']) ? filter_var($_GET['orderkey'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) : "";

$order = wc_get_order($order_id);


$pos_left = '50%';
$breite = '150mm';
$hoehe = '150mm';

$bg_img = get_option('lemontec_pdf_coupon_suject');

//position vertical
$pos_top = get_option('lemontec_pdf_coupon_pos_top');
if($pos_top == ' ') $pos_top = '150';
$pos_top .= 'mm';

//position horizontal
$pos = get_option('lemontec_pdf_coupon_position');
if($pos == 'left') {
    $position = 'left:0%;right:auto;';
} else if($pos == 'center') {
    $position = 'left:50%;transform:translateX(-50%);';
} else if($pos == 'right') {
    $position = 'right:0%;left:auto;';
} else {
    $position = 'left:50%;transform:translateX(-50%);';
}


//define fontsizes
$fs_price = get_option( 'lemontec_pdf_coupon_fontsize_price');
if($fs_price == '') $fs_price = 50;

$fs_greeting = get_option( 'lemontec_pdf_coupon_fontsize_greeting');
if($fs_greeting == '') $fs_greeting = 15 ;

$fs_couponnumber = get_option( 'lemontec_pdf_coupon_fontsize_couponnumber');
if($fs_couponnumber == '') $fs_couponnumber = 12;

$output = '';

$coupon_array = [];

//iterate order items to get ordered coupons and push it to coupon array
foreach ($order->get_items() as $item_id => $item ) {
    $coupon_title = $item->get_meta( 'Nr', true );
    
    $coupon_titles = explode(' ', $coupon_title);
    if($coupon_title != '') {
        $args_coupons = array(
            'posts_per_page'   => -1,
            'post_type'        => 'shop_coupon',
            'post_status'      => 'publish',
        );

        $lemontec_coupons = get_posts($args_coupons);
        foreach ($lemontec_coupons as $coupon) {
            foreach($coupon_titles as $ti) {
                if($coupon->post_title == $ti) { 
                    $coupon_array[$ti] = array('title' => $ti, 'amount' => $coupon->coupon_amount. ' €', 'description' => $coupon->post_content);
                }
            }
        }  
    } 
}

if(!empty($order_id) && !empty($order_key) && ($order_key == $order->get_order_key()) && !empty($coupon_array)) {  
    if($coupon_array) {
        foreach($coupon_array as $coupon) {
            $output .= '<div style="position:relative; height:100%;display:block; padding:0;margin:0;font-family:sans-serif;">';
                
                //sujet
                $output .= '<img src="'.$bg_img.'" width="210mm" height="297mm" style="position: absolute; left:0;top:0;right:0;bottom:0;height:100%;z-index:-1;    margin:0;padding:0;min-width: 100%;height: auto;">';

                $output .= '<div style="background-color: #fff; position: absolute; top: '.$pos_top.';'.$position.'text-align:center;width:50%">';
            
                    //coupon price/amount
                    $output .= '<p style="font-size: '.$fs_price.'pt;margin: 15px;">'.$coupon['amount'].'</p>';

                    //greeting
                    if($coupon['description'] != '') {
                        $description = explode(PHP_EOL,$coupon['description']);
                        
                       $output .= '<p style="font-size: '.$fs_greeting.'pt;margin: 15px;">';
                        
                        foreach($description as $key_desc => $desc) {
                            $output .=  $desc;
                            if($key_desc < (count($description)-1)) $output .= '<br>';
                        }
                       
                        $output .= '</p>';
        
                    } 
            
                    //coupon number
                    $output .= '<p style="margin: 15px;font-size: '.$fs_couponnumber.'pt;margin-top: 30px;">Nr '.$coupon['title'] . '</p>';
            
                $output .= '</div>';
            
                //copyright
                $output .= '<p style="position: absolute;bottom: 0;width: 100%;text-align: center;background-color: #fff;margin: 0;margin: 15px 15px 0 15px;box-sizing: border-box;">Powered by <img width="15px" src="' . plugin_dir_url( __DIR__ ) . '/img/lemontec_logo.png" alt="LEMONTEC WEBAGENTUR LOGO"> <a href="https://lemontec.at" target="_blank"> LEMONTEC</a></p>';
            $output .= '</div>';   
        }
    }   
} else {
    $output .= '<div style="margin:0 auto;position:relative;padding: 15px;box-sizing: border-box;font-family:sans-serif;"><p>Kein gültiger Gutschein</p>';
        $output .= '<p style="position: absolute;bottom: 0;width: 100%;text-align: center;background-color: #fff;margin: 0;margin: 15px 15px 0 15px;box-sizing: border-box;">Powered by <img width="15px" src="' . plugin_dir_url( __DIR__ ) . '/img/lemontec_logo.png" alt="LEMONTEC WEBAGENTUR LOGO"> <a href="https://lemontec.at" target="_blank"> LEMONTEC</a></p>';
    $output .= '</div>';
}

spcfw_displayHtmlAsPdf($output);