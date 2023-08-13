<?php

require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Wp_Posts_Language_Duplicator_Table extends WP_List_Table {

    // Define constructor
    function __construct() {
        parent::__construct(array(
            'singular' => 'item',   
            'plural'   => 'items',  
            'ajax'     => false     
        ));
    }

    function column_cb( $item ) {
        return sprintf('<input type="checkbox" name="items[]" value="%s" />',$item['id']);
    }
    
    // Define columns
    function get_columns() {
        return array(
            'cb'  => '<input type="checkbox" />', 
            'id'=>'ID',
            'name'=> 'Name',
            'type' => 'Type',
            'status'=> 'Status',
            'lang'=>'Language'
        );
    }

    // Populate the table rows
    function prepare_items() {
        
        $status = get_option( 'duplicated_post_statuses' ) == 'all' ? '' : get_option( 'duplicated_post_statuses' );
        $type = get_option( 'post_type_options' ) ?: [];
        $args = array(
            'post_type'      => $type, 
            'post_status'    => $status,
            'lang'           => pll_default_language(),
            'posts_per_page' => -1,
        );
        
        $query = new WP_Query($args);
        
        $data = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $data[] = array('id'=>get_the_ID(),'name'=>get_the_title(), 'type' => get_post_type(), 'status'=> get_post_status(),'lang'=>pll_get_post_language(get_the_ID()));
            }
            wp_reset_postdata();
        }

        $this->_column_headers = array($this->get_columns(), [], []);
        $this->items = $data;

        if(isset($_POST)){
            $this->process_bulk_action();
        }
    }

    function column_default($item, $column_name) {

        switch ($column_name) {
            case 'cb':
                return sprintf(
                    '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                    esc_attr($this->_args['plural']),
                    esc_attr($item['ID'])
                );
            default:
                return $item[$column_name];
        }
    }

    function get_bulk_actions() {

        $actions = array(
            'duplicate' => 'Duplicate',
        );
        return $actions;
    }

    function process_bulk_action() {

        if ('duplicate' === $this->current_action() && isset($_POST['items']) || 
            isset($_POST['bulk-duplicate']) && isset($_POST['items']) ) {

            $selected_items = isset($_POST['items']) ? $_POST['items'] : [];
            $helpers = new Wp_Posts_Language_Duplicator_Helpers();

            foreach ($selected_items as $original_page_id) {
                
                $langArray = [];
                foreach (get_option( 'list_languages_options' ) as $language) {

                    $cloned_page_id = $helpers->duplicate_page_with_acf($original_page_id);
                    if (!is_wp_error($cloned_page_id)) {
                        echo get_the_title($original_page_id) . " cloned successfully. New page ID: " . $cloned_page_id .'<br>';
                        
                        $langArray[$language] = $cloned_page_id;
                        pll_set_post_language($cloned_page_id, $language);
                    }
                    else{
                        echo $cloned_page_id->get_error_message() .'<br>';
                    }
                }

                /* connect languages */
                if(!empty($langArray)){
                    $main_language = pll_default_language();
                    $langArray[$main_language] = $original_page_id;
                    pll_save_post_translations($langArray);	
                }
            }

            return;
        }
    }
}
