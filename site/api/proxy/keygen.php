<?php
    
    /**
     * This function generates a developer key
     * based on the username + timestamp + random number
     *
     * @param string
     * @return string
     **/
    function generateDeveloperKey($usr)
    {
        $time = time();
        $rand = rand();
        return sha1($usr . $time . $rand);
    }
    
    
    
    
    if (isset($_GET['usr']))
    {
        print 'Developer key: ' . generateDeveloperKey($_GET['usr']);
    }    

/* End of file keygen.php */
/* Location: ./api/proxy/keygen.php */