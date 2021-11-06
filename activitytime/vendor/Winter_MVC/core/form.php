<?php

/**
 * MVC_Form
 *
 * @version 1.0
 *
 * @author Sandi Winter
 * @link https://github.com/sandiwinter/winter_mvc
 */
if ( ! class_exists( 'MVC_Form' ) ):

class MVC_Form {

    /**
     * messages array
     *
     * @var array
     */
    protected $messages = array();

    protected $error_messages = array();

    /**
     * rules array
     *
     * @var array
     */
    protected $rules = array();

    public function __construct()
    {
    }
    
    public function add_error_message($validation, $message)
    {
        $this->error_messages[$validation] = $message;
    }

    public function run($rules)
    {
        if(!isset($_POST))return FALSE;
        if(count($_POST)==0)return FALSE;

        $this->rules = $rules;

        foreach($rules as $key=>$rule)
        {
            $field_rules = explode('|', $rule['rules']);

            if(isset($_POST[$rule['field']]) && !empty($_POST[$rule['field']]))
            {
                foreach($field_rules as $one_rule)
                {
                    if(!empty($one_rule))
                    if(function_exists('is_'.$one_rule))
                    {
                        if(call_user_func('is_'.$one_rule, $_POST[$rule['field']]) === FALSE)
                        {
                            if(isset($this->error_messages[$one_rule]))
                            {
                                $this->messages[] = $this->error_messages[$one_rule];
                            }
                            else
                            {
                                $this->messages[] = __('Field', 'wmvc_win').' '.$rule['label'].' '.__('must be', 'wmvc_win').' '.__($one_rule, 'wmvc_win');
                            }
                        }
                    }
                    else
                    {
                        $this->messages[] = __('Missing function for rule:', 'wmvc_win').' is_'.$one_rule;
                    }
                }
            }
            elseif(in_array('required', $field_rules))
            {
                $this->messages[] = __('Field is required:', 'wmvc_win').' '.$rule['label'];
            }
        }

        if(count($this->messages) == 0)return TRUE;

        return FALSE;
    }

    public function messages($extra = 'class="alert alert-danger"', $success_message = NULL, $success_extra = 'class="alert alert-success"')
    {
        if(!isset($_POST))return FALSE;
        if(count($_POST)==0)return FALSE;

        if($success_message === NULL)
            $success_message = __('Successfuly saved', 'wmvc_win');

        if(count($this->messages) == 0)
        {
            echo '<p '.$success_extra.'>'.$success_message.'</p>';
        }

        foreach($this->messages as $key=>$message)
        {
            echo '<p '.$extra.'>'.$message.'</p>';
        }
    }


}

if(!function_exists('is_valid_email'))
{
    function is_valid_email($email)
    {
        $regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/";
        $email = strtolower($email);

        if (!preg_match($regex, $email)) {
            return FALSE;
        }
    
        return TRUE;
    }
}

if(!function_exists('is_required'))
{
    function is_required($string)
    {
        if(empty($string))
            return FALSE;
    
        return TRUE;
    }
}

if(!function_exists('is_intval'))
{
    function is_intval($string)
    {
        if(!is_numeric($string))
            return FALSE;
    
        return TRUE;
    }
}


if(!function_exists('is_valid_date'))
{
    function is_valid_date($string)
    {
        return strtotime($string) !== FALSE;
    }
}

if(!function_exists('is_sockets_enabled'))
{
    function is_sockets_enabled($string)
    {
        return function_exists('socket_create');
    }
}




endif;

?>