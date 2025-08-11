<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Db_manager
{
    var $connections = array();
    var $CI;

    function __construct()
    {
        $this->CI =& get_instance();
    }

    function get_connection($db_name)
    {
        // connection exists? return it
        if (isset($this->connections[$db_name])) 
        {
            return $this->connections[$db_name];
       }
       else
       {
            // create connection. return it.
            $this->connections[$db_name] = $this->CI->load->database($db_name, true);
            return $this->connections[$db_name];
        }
    }
}
/* End of file Access.php */
