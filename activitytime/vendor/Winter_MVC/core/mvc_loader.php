<?php

/**
 * MVC_Loader
 *
 * @version 1.0
 *
 * @author Sandi Winter
 * @link https://github.com/sandiwinter/winter_mvc
 */
if ( ! class_exists( 'MVC_Loader' ) ):

class MVC_Loader {

    /**
     * controllers_repository array
     *
     * @var array
     */
    protected $controllers_repository = array();

    /**
     * models_repository array
     *
     * @var array
     */
    protected $models_repository = array();

    /**
     * db object
     *
     * @var array
     */
    public $db = NULL;

    public $plugin_directory = NULL;

    public function __construct($plugin_directory = NULL)
    {
        $this->plugin_directory = $plugin_directory;

        require_once WINTER_MVC_PATH.'/core/helpers.php';
        require_once WINTER_MVC_PATH.'/core/input.php';
        require_once WINTER_MVC_PATH.'/core/form.php';
        require_once WINTER_MVC_PATH.'/core/database.php';
        require_once WINTER_MVC_PATH.'/core/winter_mvc_model.php';
        require_once WINTER_MVC_PATH.'/core/winter_mvc_controller.php';

        $this->db = MVC_Database::instance();
    }
    
    public function load_helper($filename)
    {
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/helpers/'.ucfirst($filename).'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/helpers/'.ucfirst($filename).'.php';
        }

        if(file_exists($file))
            require_once $file;
    }

    public function load_controller($class, $method = '', $params = array())
    {
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/controllers/'.ucfirst($class).'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/controllers/'.ucfirst($class).'.php';
        }

        if(file_exists($file))
        {
            require_once $file;

            $class = ucfirst($class);

            $class = str_replace('-', '_', $class);

            if(class_exists($class.'_index'))
            {
                $class = $class.'_index';
            }

            // Init controller
            if(!isset($this->controllers_repository[$class]))
            {
                $this->controllers_repository[$class] = new $class();
                $this->controllers_repository[$class]->set_loader($this);
            }

            // Run controller method
            if(empty($method))
                $method = 'index';

            return $this->controllers_repository[$class]->$method($params);

        }
        else
        {
            echo 'Controller file not found in: '.$file;
        }

    }

    public function view($view_file, &$data, $output = TRUE)
    {
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/views/'.$view_file.'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/views/'.$view_file.'.php';
        }

        if(file_exists($file))
        {
            if($output === TRUE)
            {
                extract($data);
                include $file;
            }
            else
            {
                ob_start();

                extract($data);
                include $file;

                $generated_output = ob_get_contents();

                ob_end_clean();

                return $generated_output;

            }

        }
        else
        {
            echo 'View file not found in: '.$file;
        }
    }

    public function model($class)
    {
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/models/'.ucfirst($class).'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/models/'.ucfirst($class).'.php';
        }
        

        if(file_exists($file))
        {
            require_once $file;

            $class = ucfirst($class);

            // Init controller
            if(!isset($this->models_repository[$class]))
            {
                $this->models_repository[$class] = new $class();
                $this->models_repository[$class]->set_loader($this);
            }

            foreach($this->controllers_repository as $key =>$rep)
            {
                $this->controllers_repository[$key]->{strtolower($class)} = &$this->models_repository[$class];
            }

            foreach($this->models_repository as $key =>$rep)
            {
                $this->models_repository[$key]->{strtolower($class)} = &$this->models_repository[$class];
            }

            $this->{strtolower($class)} = &$this->models_repository[$class];
                
        }
        else
        {
            echo 'Model file not found in: '.$file;
        }

    }

}

endif;

?>