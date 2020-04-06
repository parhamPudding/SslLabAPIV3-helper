<?php
public function getData($host){
    $sslObject = new SslLabsApi();
    $dataObject = $sslObject->scanHost($host, "on");
    //var_dump(json_decode($dataObject));
    $keep_running = true;
    while ($keep_running){
        if (isset($dataObject->status)){
            if ($dataObject->status == 'DNS'){
                sleep(5);
                $dataObject = $sslObject->scanHost($host, "on");
                 //var_dump(json_decode($dataObject));
            }else if ($dataObject->status == 'IN_PROGRESS'){
                sleep(2);
                $dataObject = $sslObject->scanHost($host, "off");
                 //var_dump(json_decode($dataObject));
            }else if ($dataObject->status == 'READY'){
                 //var_dump(json_decode($dataObject));
                $keep_running = false;
            }
        }
    }
}