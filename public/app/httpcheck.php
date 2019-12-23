<?php

foreach(array('memcached', 'mongodb', 'gd') as $ext) {
    if(!extension_loaded($ext)) {
        header('HTTP/1.0 501 Not Implemented');
        echo 'no extension '.$ext;
        exit;
    }
}

echo 'OK';
