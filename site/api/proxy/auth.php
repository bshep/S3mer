<?php


    /**
     * This function returns true if the provided key
     * is in the $developer_keys array in keys.php
     *
     * @param string
     * @return bool
     **/
    function authorize($dev_key)
    {
        if(isset($dev_key))
        {
            require_once 'keys.php';
            foreach ($developer_keys AS $item)
            {
                $valid_keys[] = $item['key'];
            }
            
            if(in_array($dev_key, $valid_keys))
            {
                // Valid key
                return true;
            } else {
                // Invalid key
                return false;
            }
            
        } else {
            // No key
            return false;
        }
    }

/* End of file auth.php */
/* Location: ./api/proxy/auth.php */