<?php
/**
 * Plugin Name: WM Private Content Plugin
 * Plugin URI: 
 * Description: Using WP's default working this plugin lets you protect your content. Its made for one thing being simple and easy.
 * Author: WiseMago
 * Author URI: wisemago.com
 * Version: 1.1.1
 */


// calling and activating the plugin by creating the table
class WMPContent {
	//on plugin activation		
	function activatePC(){
		
		 add_option('pc_msg',' <h3>Please Login</h3>
          <div class="body_content">
              <div>
                  This area is for '.get_bloginfo().' users. Please [link]click here[/link] for the user login page. 
         </div>   
          </div>');
	}

	//on plugin deactivation
	function deactivatePC(){
	
		delete_option('pc_msg');
	}
	
	//wp plugin menus
	function addMenuItemsPC(){
	
		add_menu_page('pcontent', 'Private Content',  'read', 'pc_main', array(&$this,'showPage'));
		//add_submenu_page('dri_main', 'Tests', 'Tests', 'add_report', 'dri_report', array(&$this->report,'showReport'));
		
	} 
	// include js
	function addJavascriptPC() { 
		
		if (is_admin()) { 
		  // wp_enqueue_script('gen_validatorv31', '/' . PLUGINPATH_JS.'gen_validatorv31.js');
          
		}
	} 

	// include style
	function addStylePC() { 
		  
		if (is_admin()) { 
		 //  wp_enqueue_style('css', '/'. PLUGINPATH_CSS.'doctor.css');
		}
	} 
	
    //initialize the plugin
	function __construct(){
		$_file = "private-content/" . basename(__FILE__);
		register_activation_hook($_file,array(&$this,'activatePC'));
		register_deactivation_hook($_file,array(&$this,'deactivatePC'));
		
		add_action( 'init', array($this,'addJavascriptPC' ));
		add_action( 'init', array($this, 'addStylePC' ));
		
		add_action('admin_menu', array($this, 'addMenuItemsPC'));
		
		add_shortcode('PCONTENT', array($this, 'shortcodePC'));  
	}
	
	function showPage(){
	
		if(isset($_POST['save_pc_msg'])){
			$this->save_msg($_POST);
		}
		echo "<h2>Private Content Settings</h2>"; 
		
		echo "<p>Change the text that will appear to the user when not logged in</p>";
		echo "<p>Place the following code [link]XXX[/link] where you would the link to the login page to appear. With XXX being your text. Please see the default message as an example</p>";
		
		echo "<form action='' method='post'>";
		echo wp_nonce_field( 'change-pc-msg', '_wpnonce_pc-msg' , '', false); 
		$content = get_option('pc_msg');
		wp_editor( $content, 'pc_msg', $settings = array('media_buttons'=>false,'textarea_name'=>'pc_msg','textarea_rows'=>5) );
		submit_button( 'Save Changes', 'primary', 'save_pc_msg' );
		echo "</form>";
		
	}
	//save user defined msg
	private function save_msg($post){
		
			if ( !empty($post) && check_admin_referer('change-pc-msg','_wpnonce_pc-msg') ) {
				
				update_option('pc_msg', $post['pc_msg']);
			}else{
			
				wp_die( 'Gotcha!!!' );
			}
	}
	
	function shortcodePC( $atts, $content = null ){
		/*extract(shortcode_atts(array(
		'services' => 'Lab',
		), $atts));
		$services = $atts['services']; */
		
		if($this->check_loggedin()){
              
			return $content; 
        }else{ 
			$pc_msg = get_option('pc_msg');	  
				
			$a_start = '<a href="'.wp_login_url( get_permalink()).'">';	
			$a_end = '</a>';	
			
			$pcontent = str_replace('[link]',$a_start,$pc_msg);
			$pcontent = str_replace('[/link]',$a_end,$pcontent);
			
			return $pcontent;
		}
	}
	
	protected function check_loggedin() {
		global $user_ID;
		if($user_ID) {
			return true;
		} else {
			return false;
		}
	}
}
$oPC = new WMPContent();
             ?>