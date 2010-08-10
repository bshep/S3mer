<?php
    
    include 'auth.php';
    
    if (isset($_GET['key']) && isset($_GET['url']) && authorize($_GET['key'])) {
        
        $url = urldecode($_GET['url']);
        
        $post_data = $HTTP_RAW_POST_DATA;
        $header[] = "Content-type: text/xml";
        $header[] = "Content-length: ".strlen($post_data);
    
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
        if (strlen($post_data) > 0)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
    
        $response = curl_exec($ch);     
    
        if (curl_errno($ch))
        {
            echo curl_error($ch);
        } else {
            curl_close($ch);
            echo $response;
        }
    } else {
        header('HTTP/1.0 401 Unauthorized');
        echo 'HTTP/1.0 401 Unauthorized';
    }

/* End of file auth.php */
/* Location: ./api/proxy/auth.php */