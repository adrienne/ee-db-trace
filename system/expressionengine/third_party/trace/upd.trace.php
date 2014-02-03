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

class Trace_upd
{

    var $version = '1.0';

    function __construct()
    {
        $this->EE = & get_instance();
    }

    function install()
    {
        $this->EE->load->dbforge();

        $data = array(
            'module_name' => 'Trace',
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n'
        );

        $this->EE->db->insert('modules', $data);


        $data = array(
            'class' => 'Trace',
            'method' => 'test'
        );

        $this->EE->db->insert('actions', $data);


        if(!$this->EE->db->table_exists('trace_releases'))
        {

            $fields = array(
                'release' => array('type' => 'varchar', 'constraint' => '255'),
                'developer' => array('type' => 'varchar', 'constraint' => '50'),
                'date' => array('type' => 'int', 'constraint' => '10'),
                'description' => array('type' => 'varchar', 'constraint' => '250', 'null' => TRUE, 'default' => NULL)
            );

            $this->EE->dbforge->add_field($fields);

            $this->EE->dbforge->create_table('trace_releases');
        }
        
        return TRUE;
    }

    // --------------------------------------------------------------------
    /**
     * Module Uninstaller
     *
     * @access	public
     * @return	bool
     */
    function uninstall()
    {
        $this->EE->load->dbforge();

        $this->EE->db->select('module_id');
        $query = $this->EE->db->get_where('modules', array('module_name' => 'Trace'));

        $this->EE->db->where('module_id', $query->row('module_id'));
        $this->EE->db->delete('module_member_groups');

        $this->EE->db->where('module_name', 'Trace');
        $this->EE->db->delete('modules');

        $this->EE->db->where('class', 'Trace');
        $this->EE->db->delete('actions');

        $this->EE->dbforge->drop_table('trace_releases');

        return TRUE;
    }

    // --------------------------------------------------------------------
    /**
     * Module Updater
     *
     * @access	public
     * @return	bool
     */
    function update($current = '')
    {
        return TRUE;
    }
}