<?php
/**
 * Plugin Name: Netfunda Contact Form
 *Plugin URI: http://debi.temp.netfunda.com/Netfunda-Contact-Form.zip
 * Description: This is a contact form plugin By Debi Prasad Sinha. You can use shortcode [paste-it-go-get-contact-form] to display form on any page in the wordpress.When activated and the short code is paste in any of your page it simply create a table wpcontract in your wordpress database and after submitting the form it stores the data in the wpcontract table .For more info mail me at debiprasad.sinha@netfunda.com Thank You for using it.
 * Description: This is a contact form plugin y . You can use shortcode [paste-it-go-get-contact-form] to display form on any page in the wordpress.When activated and the short code is paste in any of your page it simply create a table wpcontract in your wordpress database and after submitting the form it stores the data in the wpcontract table .For more info mail me at debiprasad.sinha@netfunda.com.Thank You for using it.
 * Version: 2.0.0
 * Author: Debi Prasad Sinha
 * Author URI: http://netfunda.com
 * License: GNU General Public License v3 or later
 * License URI: http://www.http://debi.temp.netfunda.com/Netfunda-Contact-Form.zip
 * Text Domain: netfunda
 * Domain Path: cf
 */


// Load the plugin's text domain



function contact_init() { 
	load_plugin_textdomain( 'netfunda', false, dirname( plugin_basename( __FILE__ ) ) . '/cf' );
}
add_action('plugins_loaded', 'contact_init');
 

// Enqueues plugin scripts
function contact_scripts() {	
	if(!is_admin())
	{
		wp_enqueue_style('contact_style', plugins_url('contact_style.css',__FILE__));
	}
}
add_action('wp_enqueue_scripts', 'contact_scripts');
include 'contact_main.php';


global $wpdb;

$tablename = "wpcontract";   
$query = "CREATE TABLE IF NOT EXISTS`" . $tablename . "` (
    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
    `name` varchar(60) NOT NULL,
    `email` varchar(60) NOT NULL,
    `subject` varchar(60) NOT NULL,
    `message` varchar(60) NOT NULL,
    PRIMARY KEY  (`id`)
    );";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($query);

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



class My_Example_List_Table extends WP_List_Table {
         var $example_data;
    
    function __construct(){
   

        parent::__construct( array(
            'singular'  => __( 'Contact', 'mylisttable' ),     //singular name of the listed records
            'plural'    => __( 'Contacts', 'mylisttable' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?

    ) );


    add_action( 'admin_head', array( &$this, 'admin_header' ) );            

    }

  function admin_header() {

  }




  function no_items() {
    _e( 'No Contacts found, dude.' );
  }





  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'id':
        case 'name':
        case 'email':
        case 'subject':
        case 'message':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }





function get_sortable_columns() {
  $sortable_columns = array(
    'id'  => array('id',false),
    'name' => array('name',false),
    'email'   => array('email',false),
    'subject' => array('subject',false),
    'message' => array('message',false)
  );
  return $sortable_columns;
}




function get_columns(){
  global $wpdb;
$querydata = $wpdb->get_results(
  "
  SELECT * FROM wpcontract
  "
  );
//echo '<pre>';print_r($querydata);echo '</pre>';
foreach ($querydata as $key => $value) {
  foreach ($value as $key1 => $value1) {
        
         if($key1=='id')
        $this->example_data[$key]['id']=$value1;
        if($key1=='name')
        $this->example_data[$key]['name']=$value1;
        if($key1=='email')
         $this->example_data[$key]['email']=$value1;
        if($key1=='subject')
         $this->example_data[$key]['subject']=$value1;
        if($key1=='message')
        $this->example_data[$key]['message']=$value1;
  }
  
}

 foreach ($querydata as $querydatum ) {
   
$columns = array(
            'cb'        => '<input type="checkbox" />',
            'id' => __( 'ID', 'mylisttable' ),
            'name'    => __( 'Name', 'mylisttable' ),
            'email'      => __( 'Email', 'mylisttable' ),
            'subject'      => __( 'Subject', 'mylisttable' ),
            'message'      => __( 'Message', 'mylisttable' )

        );
         return $columns;
    }
}

function usort_reorder( $a, $b ) {

  // If no sort, default to title

  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';

  // If no order, default to asc

  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

  // Determine sort order

  $result = strcmp( $a[$orderby], $b[$orderby] );

  // Send final sort direction to usort
  
  return ( $order === 'asc' ) ? $result : -$result;
}




function column_id($item){
  $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&book=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );

  return sprintf('%1$s %2$s', $item['id'], $this->row_actions($actions) );
}





function get_bulk_actions() {
  $actions = array(
    'delete'    => 'Delete'
  );
  return $actions;
}

function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="book[]" value="%s" />', $item['ID']
        );    
    }



function prepare_items() {
  $columns  = $this->get_columns();
  $hidden   = array();
  $sortable = $this->get_sortable_columns();
  $this->_column_headers = array( $columns, $hidden, $sortable );
  usort( $this->example_data, array( &$this, 'usort_reorder' ) );
  
  $per_page = 5;
  $current_page = $this->get_pagenum();
  $total_items = count( $this->example_data );

  // only ncessary because we have sample data
  $this->found_data = array_slice( $this->example_data,( ( $current_page-1 )* $per_page ), $per_page );

  $this->set_pagination_args( array(
    'total_items' => $total_items,                  //WE have to calculate the total number of items
    'per_page'    => $per_page                     //WE have to determine how many items to show on a page
  ) );
  $this->items = $this->found_data;
}

} //class



function my_add_menu_items(){
  $hook = add_menu_page( 'Netfunda Contact Data', 'Contact Data', 'activate_plugins', 'my_list_test', 'my_render_list_page' );
  add_action( "load-$hook", 'add_options' );
}


function add_options() {
  global $myListTable;
  $option = 'per_page';
  $args = array(
         'label' => 'Contacts',
         'default' => 10,
         'option' => 'Contacts_per_page'
         );
  add_screen_option( $option, $args );
  $myListTable = new My_Example_List_Table();
}
add_action( 'admin_menu', 'my_add_menu_items' );



function my_render_list_page(){
  

  global $myListTable;
  echo '</pre><div class="wrap"><h2>My List Table Test</h2>'; 
  $myListTable->prepare_items(); 
?>
  <form method="post">
    <input type="hidden" name="page" value="ttest_list_table">
    <?php
    $myListTable->search_box( 'search', 'search_id' );

  $myListTable->display(); 
  echo '</form></div>'; 
}
