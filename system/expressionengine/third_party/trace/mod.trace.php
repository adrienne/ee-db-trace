<?php
/**
 * @package		DB Trace Module
 * @author		Frans Cooijmans, dWise
 * @copyright           Copyright (c) 2014 dWise
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt
 * @link		http://www.dwise.nl
 */
 
if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trace
{
    private $page_load_id = FALSE;

    function __construct()
    {
        $this->EE = & get_instance();
        $this->EE->load->add_package_path(APPPATH . 'third_party/trace/');

        $this->page_load_id = md5($_SERVER["REQUEST_URI"] . '|' . gmmktime());
    }

    function add_query($sql, $insert_id, $affected_rows)
    {
        $sql_words = explode(" ", strtolower($sql));
        $default_exclude = array('exp_trace_releases');
        $commands_include = $this->EE->config->item('trace_include');
        $table_exclude = array_values(array_merge($default_exclude, $this->EE->config->item('trace_exclude')));
        $log = false;

        // @TODO: replace with nice REGEX
        foreach($commands_include as $include)
        {
            if(in_array($include, $sql_words) || in_array("`$include`", $sql_words))
            {
                $log = true;
                break;
            }
        }

        // @TODO: replace with nice REGEX
        foreach($table_exclude as $include)
        {
            if(in_array($include, $sql_words) || in_array("`$include`", $sql_words))
            {
                $log = false;
                break;
            }
        }

        if($log && @$this->EE->session->cache('trace', 'status') != 'publishing')
        {
            $this->EE->load->model('xml_trace_model');

            $file = APPPATH . "third_party/trace/files/" . $this->EE->config->item('trace_developer') . ".xml";

            if($this->EE->xml_trace_model->load($file, TRUE))
            {
                $this->EE->xml_trace_model->insert_query($this->page_load_id, $sql, $insert_id, $affected_rows);
                $this->EE->xml_trace_model->save();
            }
        }
    }
}