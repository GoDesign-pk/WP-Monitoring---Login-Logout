<?php

if ( ! function_exists('echo_js'))
{
    function echo_js($str)
    {
        $str = str_replace("'", "\'", trim($str));
        $str = str_replace('"', '\"', $str);
        
        echo $str;
    }
}


if ( ! function_exists('_js'))
{
    function _js($str)
    {
        $str = str_replace("\\", "", trim($str));
        $str = str_replace("'", "\'", trim($str));
        $str = str_replace('"', '\"', $str);
        
        return $str;
    }
}

if ( ! function_exists('dump'))
{
    function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

if ( ! function_exists('resolve_ip'))
{
    function resolve_ip($ip)
    {
        if($ip == 'DISABLED')
            return $ip;

        if($ip == '::1')
        {
            return $ip.', local';
        }

        $str = '<a target="_blank" href="https://whatismyipaddress.com/ip/'.$ip.'">'.$ip.'</a>';
        
        return $str;
    }
}

if ( ! function_exists('stringify_attributes'))
{
/**
 * Stringify attributes for use in html tags.
 *
 * Helper function used to convert an array or object of
 * attributes to a string
 *
 * @param	mixed
 * @return	string
 */
function stringify_attributes($attributes, $js = FALSE)
{
	if (is_object($attributes) && count($attributes) > 0)
	{
		$attributes = (array) $attributes;
	}

	if (is_array($attributes))
	{
		$atts = '';
		if (count($attributes) === 0)
		{
			return $atts;
		}
		foreach ($attributes as $key => $val)
		{
			if ($js)
			{
				$atts .= $key.'='.$val.',';
			}
			else
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}
		}
		return rtrim($atts, ',');
	}
	elseif (is_string($attributes) && strlen($attributes) > 0)
	{
		return ' '.$attributes;
	}

	return $attributes;
}
}

if ( ! function_exists('anchor'))
{
	/**
	 * Anchor Link
	 *
	 * Creates an anchor based on the local URL.
	 *
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = is_array($uri)
			? site_url($uri)
			: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

		if ($title === '')
		{
			$title = $site_url;
		}

		if ($attributes !== '')
		{
			$attributes = stringify_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

if ( ! function_exists('btn_edit'))
{
    function btn_edit($uri)
    {
        return anchor($uri, '<i class="glyphicon glyphicon-pencil"></i>', array('class'=>'btn btn-success btn-xs'));
    }
}

if ( ! function_exists('btn_read'))
{
    function btn_read($uri, $title=NULL)
    {
        if(empty($title))$title=__('Read', 'wmvc_win');
        
        return anchor($uri, '<i class="glyphicon glyphicon-search"></i> '.$title, array('class'=>'btn btn-primary btn-xs'));
    }
}

if ( ! function_exists('btn_open'))
{
    function btn_open($uri, $target=NULL)
    {
        if($target === NULL)
            $target = '_blank';

        return anchor($uri, '<i class="glyphicon glyphicon-search"></i>', array('class'=>'btn btn-primary btn-xs', 'target'=>$target, 'title'=>__('Open details', 'wmvc_win')));
    }
}

if ( ! function_exists('btn_open_ajax'))
{
    function btn_open_ajax($uri, $target=NULL)
    {
        if($target === NULL)
            $target = '_blank';

        return anchor($uri, '<i class="glyphicon glyphicon-search"></i>', array('class'=>'btn btn-primary btn-xs popup-with-form-ajax', 'target'=>$target, 'title'=>__('Open details', 'wmvc_win')));
    }
}

if ( ! function_exists('btn_delete_noconfirm'))
{
    function btn_delete_noconfirm($uri)
    {
        return anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array('class'=>'btn btn-danger btn-xs delete_button'));
    }
}

if ( ! function_exists('btn_delete'))
{
    function btn_delete($uri, $confirm_question = TRUE, $title='')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '';
        }

        if($confirm_question)
        {
            return anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array( 'target' => $target,  'title' => $title, 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-danger btn-xs delete_button'));
        }
        else
        {
            return anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array( 'target' => $target,  'title' => $title, 'class'=>'btn btn-danger btn-xs delete_button'));
        }
    }
}

if ( ! function_exists('btn_save'))
{
    function btn_save($uri, $empty = '-empty')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '';
        }

        return anchor($uri, '<i class="glyphicon glyphicon-heart'.$empty.'"></i> ', array( 'target' => $target, 'class'=>'btn btn-danger btn-xs save_button', 'title'=>__('Save as Favourite for further analysis', 'wmvc_win')));
    }
}

if ( ! function_exists('btn_block'))
{
    function btn_block($uri, $confirm_question = FALSE, $title='')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '_blank';
        }

        if($confirm_question)
        {
            return anchor($uri, '<i class="glyphicon glyphicon-lock"></i> ', array( 'target' => $target, 'title' => $title, 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-warning btn-xs block_button'));
        }
        else
        {
            return anchor($uri, '<i class="glyphicon glyphicon-lock"></i> ', array( 'target' => $target, 'title' => $title, 'class'=>'btn btn-warning btn-xs block_button'));
        }
    }
}


if ( ! function_exists('btn_view'))
{
    function btn_view($uri, $confirm_question = FALSE, $title='')
    {
        if($confirm_question)
        {
            return anchor($uri, '<i class="glyphicon glyphicon-search"></i> ', array( 'title' => $title, 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-info btn-xs'));
        }
        else
        {
            return anchor($uri, '<i class="glyphicon glyphicon-search"></i> ', array( 'title' => $title, 'class'=>'btn btn-info btn-xs'));
        }
    }
}

if ( ! function_exists('btn_hide'))
{
    function btn_hide($uri)
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '_blank';
        }

        return anchor($uri, '<i class="glyphicon glyphicon-eye-close"></i> ', array( 'target' => $target, 'class'=>'btn btn-default btn-xs', 'title'=>__('Define hide rules', 'wmvc_win')));
    }
}

if ( ! function_exists('get_file_extension'))
{
    function get_file_extension($filepath)
    {
        return substr($filepath, strrpos($filepath, '.')+1);
    }
}

if ( ! function_exists('character_hard_limiter'))
{
    function character_hard_limiter($string, $max_len)
    {
        if(strlen($string)>$max_len)
        {
            return substr($string, 0, $max_len-3).'...';
        }
        
        return $string;
    }
}


if(!function_exists('wmvc_count')) {
    function wmvc_count($mixed='') {
        $count = 0;
        
        if(!empty($mixed) && (is_array($mixed))) {
            $count = count($mixed);
        } else if(!empty($mixed) && function_exists('is_countable') && version_compare(PHP_VERSION, '7.3', '<') && is_countable($mixed)) {
            $count = count($mixed);
        }
        else if(!empty($mixed) && is_object($mixed)) {
            $count = 1;
        }
        return $count;
    }
}

function wmvc_is_user_in_role( $user, $role  ) {
    return in_array( $role, $user->roles );
}

function wmvc_is_logged_user()
{
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) {
        return false;
    } else {
        // Logged in.
        return true;
    }
}

function wmvc_get_current_user_role() {
    if( is_user_logged_in() ) {
      $user = wp_get_current_user();
      $role = ( array ) $user->roles;
      return $role[0];
    } else {
      return '';
    }
   }

function wmvc_user_in_role($role)
{
    if(!wmvc_is_logged_user())
        return false;
    
    $current_user = wp_get_current_user();
    
    return wmvc_is_user_in_role( $current_user, $role  );
}

function wmvc_character_limiter($text, $limit)
{
    if(is_array($text))
        $text = 'Array( '. join(', ', $text).' )';
        
    if(strlen($text) > $limit)
    {
        return substr($text, 0, $limit-4).'...';
    }

    return $text;
}

function wmvc_show_data($field_name, &$db_value = NULL, $default = '')
{
    if(isset($_POST[$field_name]))
        return wmvc_xss_clean($_POST[$field_name]);

    if(is_array($db_value))
    {
        if(isset($db_value[$field_name]))
        {
            return wmvc_xss_clean($db_value[$field_name]);
        }
        else
        {
            return $default;
        }
    }

    if(is_object($db_value))
    {
        if(isset($db_value->$field_name))
        {
            return wmvc_xss_clean($db_value->$field_name);
        }
        else
        {
            return $default;
        }
    }

    if(!empty($db_value))
        return wmvc_xss_clean($db_value);  

    return wmvc_xss_clean($default);
}


/*
<select class="form-control" name="<?php echo 'control_operator_'.$i_fieldnum; ?>">
    <option value="CONTAINS">CONTAINS</option>
    <option value="NOT_CONTAINS">NOT_CONTAINS</option>
</select>
*/
function wmvc_select_option($field_name, $options = array(), $selected = NULL, $extra)
{
    $output = '<select name="'.$field_name.'" '.$extra.' >';

    if(is_array($options) && count($options) > 0)
    foreach($options as $key=>$val)
    {
        $output.= '<option value="'.$key.'" '.($selected==$key?'selected':'').'>'.$val.'</option>';
    }

    $output.= '</select>';

    return $output;
}

function wmvc_select_radio($field_name, $options = array(), $selected = NULL)
{
    $output = ''; //'<div class="radio"> <select name="'.$field_name.'" '.$extra.' >';

    if(is_array($options) && count($options) > 0)
    foreach($options as $key=>$val)
    {
        $output.= '<div class="radio">';
        $output.= '<label>';
        $output.= '<input type="radio" name="'.$field_name.'" id="optionsRadios1_format" value="'.$key.'" '.($selected==$key?'checked':'').' />';
        $output.= $val;
        $output.= '</label>';
        $output.= '</div>';
    }

    return $output;
}


function wmvc_form_messages()
{
    echo 'ok';
}

function wmvc_xss_clean($data)
{
    //if($data == '0000-00-00 00:00:00')return '';
    if(is_array($data))
        return wmvc_xss_clean_array($data);

    if(is_object($data))
        return wmvc_xss_clean_object($data);

    if($data === NULL)
        return '';

    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    $data = sanitize_text_field($data);

    // we are done...
    return $data;
}

function wmvc_xss_clean_array($array)
{
    if(!is_array($array))return array();

    $arr_cleaned = array();
    foreach($array as $key=>$val)
    {
        if($key == 'newcontent')
        {
            $arr_cleaned[wmvc_xss_clean($key)] = $val;
        }
        else
        {
            $arr_cleaned[wmvc_xss_clean($key)] = wmvc_xss_clean($val);
        }
        
    }

    //dump($arr_cleaned);

    return $arr_cleaned;
}

function wmvc_xss_clean_object($object)
{
    if(!is_object($object))return NULL;

    $array = get_object_vars($object);
    foreach($array as $key=>$val)
    {
        $object->{$key} = wmvc_xss_clean($object->{$key});
    }

    return $object;
}

function wmvc_roles_array() {
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        $sub['role'] = esc_attr($role);
        $sub['name'] = translate_user_role($details['name']);
        $roles[] = $sub;
    }
    return $roles;
}

function wmvc_seconds_to_hms($seconds, $show_days=true) {
    $t = round($seconds);
    $days = floor($t/86400);
    $day_sec = $days*86400;
    $hours = floor( ($t-$day_sec) / (60 * 60) );
    $hour_sec = $hours*3600;
    $minutes = floor((($t-$day_sec)-$hour_sec)/60);
    $min_sec = $minutes*60;
    $sec = (($t-$day_sec)-$hour_sec)-$min_sec;

    if($show_days)
    {
        return sprintf('%02d:%02d:%02d:%02d', $days, $hours, $minutes, $sec);
    }
    
    // to show only hours, no days
    $hours = floor( ($t) / (60 * 60) );
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $sec);
}

function wmvc_xml_encode($array) {
    if (is_null($DOMDocument)) {
        $DOMDocument =new DOMDocument;
        $DOMDocument->formatOutput = true;
        wmvc_xml_encode($mixed, $DOMDocument, $DOMDocument);
        echo $DOMDocument->saveXML();
    }
    else {
        // To cope with embedded objects 
        if (is_object($mixed)) {
          $mixed = get_object_vars($mixed);
        }
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    }
                    else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                }
                else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }

                wmvc_xml_encode($mixedElement, $node, $DOMDocument);
            }
        }
        else {
            $mixed = is_bool($mixed) ? ($mixed ? 'true' : 'false') : $mixed;
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}

function wmvc_add_wp_image($filename_source)
{
    $file = $filename_source;
    $filename = basename($file);
    
    $parent_post_id = 0;
    
    $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
    if (!$upload_file['error']) {
    	$wp_filetype = wp_check_filetype($filename, null );
    	$attachment = array(
    		'post_mime_type' => $wp_filetype['type'],
    		'post_parent' => $parent_post_id,
    		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
    		'post_content' => '',
    		'post_status' => 'inherit'
    	);
    	$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
    	if (!is_wp_error($attachment_id)) {
    		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
    		wp_update_attachment_metadata( $attachment_id,  $attachment_data );
            
            return $attachment_id;
    	}
    }
    
    return NULL;
}




?>