<?php

    spl_autoload_register( function( $class_name ) {

        // only load Activity Feed Anywhere classes
        if( strpos($class_name, 'BS_Activity_Feed_Anywhere') !== false ) {

	        $class_folder = 'classes';

            // format class name
            $class_file_name = str_replace('_', '-', strtolower($class_name) );

            // build the class file name 
            $file_path = BS_AFA_PLUGIN_PATH . $class_folder . '/class-'. $class_file_name . '.php';

            if( file_exists($file_path) ) {
                include $file_path;
            }
            
       
        }
    });

?>