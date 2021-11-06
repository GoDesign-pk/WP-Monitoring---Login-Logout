<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Actt_current_active extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{

        // Load view
        $this->load->view('actt_current_active/index', $this->data);
    }

	// Called from ajax
	// json for datatables
	public function datatable()
	{
		$this->enable_error_reporting();

        // configuration
        $columns = array('idvisited_pages', 'request_uri', 'title', 'user_info', 'time_start', 'time_end');
        $controller = 'visitedpages';
        
        // Fetch parameters
        $parameters = $this->input->post();
        $draw = $this->input->post_get('draw');
        $start = $this->input->post_get('start');
        $length = $this->input->post_get('length');
		$search = $this->input->post_get('search');

        if(isset($search['value']))
			$parameters['searck_tag'] = $search['value'];
			
		$this->load->model($controller.'_m');

        $recordsTotal = $this->{$controller.'_m'}->total_lang(array('is_visit_end = 0'=>NULL, 'time_end !='=>'0000-00-00 00:00:00'), NULL);
        
        actt_prepare_search_query_GET($columns, $controller.'_m');
        $recordsFiltered = $this->{$controller.'_m'}->total_lang(array('is_visit_end = 0'=>NULL, 'time_end !='=>'0000-00-00 00:00:00'), NULL);
        
        actt_prepare_search_query_GET($columns, $controller.'_m');
        $data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, array('is_visit_end = 0'=>NULL, 'time_end !='=>'0000-00-00 00:00:00'));

        $query = $this->db->last_query();

        // Add buttons
        foreach($data as $key=>$row)
        {
            foreach($columns as $val)
            {
                if(isset($row->$val))
                {
                    
                }
                elseif(isset($row->json_object))
                {
                    $json = json_decode($row->json_object);
                    if(isset($json->$val))
                    {
                        $row->$val = $json->$val;
                    }
                    else
                    {
                        $row->$val = '-';
                    }
                }
                else
                {
                    $row->$val = '-';
                }
            }

            if(current_time( 'timestamp' ) - strtotime( $row->time_end ) > 60)
            {
                $row->time_end = '<span style="color:red">'.$row->time_end.'</span>';
            }

            $options = '';//btn_edit(admin_url("admin.php?page=actt_add_graph&id=".$row->{"id$controller"})).' ';

            $row->edit = $options;

            $row->checkbox = '';
        }

        //format array is optional
        $json = array(
                "parameters" => $parameters,
                "query" => $query,
                "draw" => $draw,
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data
                );

        //$length = strlen(json_encode($data));
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        //header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length);
        echo json_encode($json);
        
        exit();
    }
    
    public function bulk_remove($id = NULL, $redirect='1')
	{   
        $this->load->model('visitedpages_m');

        // Get parameters
        $visited_pages_ids = $this->input->post('visited_pages_ids');

        $json = array(
            "visited_pages_ids" => $visited_pages_ids,
            );

        foreach($visited_pages_ids as $id)
        {
            if(is_numeric($id))
                $this->visitedpages_m->delete($id);
        }

        echo json_encode($json);
        
        exit();
    }

    public function export_csv_current()
    {
        ob_clean();

        $controller = 'visitedpages';
            
        $this->load->model($controller.'_m');
        
        $data = $this->{$controller.'_m'}->get_pagination_lang(NULL, NULL, array('is_visit_end = 0'=>NULL, 'time_end !='=>'0000-00-00 00:00:00'));

        $gmt_offset = get_option('gmt_offset');

        foreach($data as $key=>$row)
        {
            if(empty($row->time_sec_total))
                $row->time_sec_total = (string) (strtotime($row->time_end) - strtotime($row->time_start));

            $row->user_info = strip_tags($row->user_info);

            $data[$key] = $row;
        }

        $skip_cols = array('other_data');
        
        $print_data = actt_prepare_export($data, $skip_cols);

        header('Content-Type: application/csv');
        header("Content-Length:".strlen($print_data));
        header("Content-Disposition: attachment; filename=csv_current_".date('Y-m-d-H-i-s', time()+$gmt_offset*60*60).".csv");

        echo $print_data;
        
        exit();
    }
    
    
    public function filter_save ($id = NULL, $redirect='1') {
        $this->enable_error_reporting();

        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $parameters = $_POST;
        $name_val = 'activitytime_list_save_search_filter_Userid'.get_current_user_id();
        $options = get_option( $name_val );
        $filter_name = $parameters['filter_name'];
        $filter_par = $parameters['filter_param'];
        
        $json_string = $filter_par;

        $json_string = stripslashes($json_string); 
        $filter_par = json_decode($json_string);
        
        $options[] = [
            'name'=> $filter_name,
            'filter_par'=> serialize($filter_par)
        ];
        
        update_option($name_val, $options);
        
        $ajax_output['success'] = true;
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    
    
    public function filter_get ($id = NULL, $redirect='1') {
        $this->enable_error_reporting();
    
        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $parameters = $_POST;
        $name_val = 'activitytime_list_save_search_filter_Userid'.get_current_user_id();
        $options = get_option( $name_val );
        
        $results = [];
        if(!empty($options))
        foreach ($options as $key => $filter) {
            $results[] = [
                'filterid'=> $key,
                'name'=> $filter['name'],
                'filter_par'=> json_encode(unserialize($filter['filter_par']))
            ];
            
        }
        
        $ajax_output['results'] = $results;
        $ajax_output['success'] = true;
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    
    public function filter_remove ($id = NULL, $redirect='1') {
        $this->enable_error_reporting();
        
        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $parameters = $_POST;
        
        $name_val = 'activitytime_list_save_search_filter_Userid'.get_current_user_id();
        $options = get_option( $name_val );
        
        if(!empty($options) && isset($options[$parameters['filter_id']])) {
            unset($options[$parameters['filter_id']]);
            update_option($name_val, $options);
        }
        
        $ajax_output['success'] = true;
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    

    
}
