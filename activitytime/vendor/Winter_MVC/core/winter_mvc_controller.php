<?php


if ( ! class_exists( 'Winter_MVC_Controller' ) ):

    class Winter_MVC_Controller {
    
        /**
         * data array
         *
         * @var array
         */
        protected $data = array();

        protected $load = NULL;

        protected $input = NULL;

        /**
         * db object
         *
         * @var array
         */
        protected $db = NULL;


        public function __construct(){
            global $Winter_MVC;

            $this->load = &$Winter_MVC;

            $this->input = new MVC_Input();

            $this->form = new MVC_Form();

            $this->db = MVC_Database::instance();
            
        }

        public function set_loader($load)
        {
            if(!empty($load))
                $this->load = &$load;
        }

        public function enable_error_reporting()
        {
            ini_set('display_errors', 1);
		    ini_set('display_startup_errors', 1);
		    error_reporting(E_ALL);
        }

    }
    
    endif;







?>