<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Actt_sessions extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{

        // Load view
        $this->load->view('actt_sessions/index', $this->data);
    }

	// Called from ajax
	// json for datatables
	public function datatable()
	{
		$this->enable_error_reporting();

        // configuration
        $columns = array('iduser_sessions', 'user_info', 'time_start', 'time_end', 'time_sec_total');
        $controller = 'usersessions';
        
        // Fetch parameters
        $parameters = $this->input->post();
        $draw = $this->input->post_get('draw');
        $start = $this->input->post_get('start');
        $length = $this->input->post_get('length');
		$search = $this->input->post_get('search');

        if(isset($search['value']))
			$parameters['searck_tag'] = $search['value'];
			
		$this->load->model($controller.'_m');

        $recordsTotal = $this->{$controller.'_m'}->total_lang(array('time_end !='=>'0000-00-00 00:00:00'), NULL);
        
        actt_prepare_search_query_GET($columns, $controller.'_m');
        $recordsFiltered = $this->{$controller.'_m'}->total_lang(array('time_end !='=>'0000-00-00 00:00:00'), NULL);
        
        actt_prepare_search_query_GET($columns, $controller.'_m');
        $data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, array('time_end !='=>'0000-00-00 00:00:00'));

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

            if($row->is_visit_end == 0)
            {
                $row->time_sec_total = intval(strtotime($row->time_end) - strtotime($row->time_start));

                $row->time_start = '<span style="color:green">'.$row->time_start.'</span>';
                $row->time_end = '<span style="color:green">'.$row->time_end.'</span>';
            }

            if(empty($row->time_sec_total))
            {
                $row->time_sec_total = '-';
            }
            else
            {
                $init = $row->time_sec_total;
                $minutes = floor(($init / 60));
                $seconds = $init % 60;

                $row->time_sec_total = "$minutes:$seconds";
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
        $this->load->model('usersessions_m');

        // Get parameters
        $user_sessions_ids = $this->input->post('user_sessions_ids');

        $json = array(
            "user_sessions_ids" => $user_sessions_ids,
            );

        foreach($user_sessions_ids as $id)
        {
            if(is_numeric($id))
                $this->usersessions_m->delete($id);
        }

        echo json_encode($json);
        
        exit();
    }  


    public function export_csv_sessions()
    {
        ob_clean();

        $controller = 'usersessions';
            
        $this->load->model($controller.'_m');
        
        $data = $this->{$controller.'_m'}->get_pagination_lang(NULL, NULL, array('time_end !='=>'0000-00-00 00:00:00'));

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
        header("Content-Disposition: attachment; filename=csv_sessions_".date('Y-m-d-H-i-s', time()+$gmt_offset*60*60).".csv");

        echo $print_data;
        
        exit();
    }

    
}
