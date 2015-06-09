<?php

 
$ip_block_array = array(
    '157.55.39.122',
    '207.46.13.62',
    '207.46.13.91',
    '17.228.4.82',
    '50.17.56.146',
    '54.145.46.246',
    '23.21.99.123',
    '17.228.4.81'
);

if(isset($_SERVER['REMOTE_ADDR']) && array_search($_SERVER['REMOTE_ADDR'], $ip_block_array) !== false){
    // This is attack ip addresses. Get them away!
    exit();
}