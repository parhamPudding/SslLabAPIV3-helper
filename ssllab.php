<?php

class SslLabs {
    /**
     * @const string API_URL
     */
    const API_URL = "https://api.ssllabs.com/api/v3/";
    /**
     * @var string $host
     */
    private $host;
    /**
     * @var array $log
     */
    private $log;
    /**
     * SslLabsApi constructor.
     * @param bool $debug
     */
    public function __construct() {}
    /**
     * @method getInfo
     * @return mixed
     */
    public function getInfo() {
        $this->getData('info');
    }
    /**
     * @method scanHost
     * @param $host
     * @param string $startNew
     * @param string $fromCache
     * @param string $publish
     * @param int $maxAge
     * @param string $all
     * @param string $ignoreMismatch
     * @return mixed
     */
    public function scanHost($host, $startNew = "on", $fromCache = "off", $publish = "off", $maxAge = 0, $all = null, $ignoreMismatch = "on"){
        $this->host = $host;
        $response =  $this->getData('analyze', [
                                'host'      => $host,
                                'publish'   => $publish,
                                'startNew'  => $startNew,
                                'fromCache' => $fromCache,
                                'maxAge'    => $maxAge,
                                'all'       => $all,
                                'ignoreMismatch' => $ignoreMismatch]);
        return ($response);
    }
    /**
     * @mthod getEndpoint
     * @param $ip
     * @param null $host
     * @param string $fromCache
     * @return mixed
     */
    public function getEndpoint($ip, $host = null, $fromCache = "off") {
        $host = $host ? $host : $this->host;
        $this->apiCall['getEndpointData'] = ['host' => $host, 's' => $ip, 'fromCache' => $fromCache];
        return $this->getData('getEndpointData');
    }
    /**
     * @method getStatusCode
     * @return mixed
     */
    public function getStatusCode() {
        return ($this->getData('getStatusCodes'));
    }
    /**
     * @method getCertRaw
     * @param null $trustStore
     * @return mixed
     */
    public function getCertRaw($trustStore = null) {
        return $this->getData('getRootCertsRaw', ['trustStore' => $trustStore]);
    }

    /**
     * @method getData
     * @param $apiCall
     * @param array $parameters
     * @return mixed
     */
    public function getData($apiCall, $parameters = []) {
        $url = $this->generateURL($apiCall, $parameters);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_ENCODING       => "",
            CURLOPT_USERAGENT      => "client",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content  = curl_exec($ch);
        curl_close($ch);
        /**
         * Debug
         */
        $this->log['apiCall'] = $apiCall;
        $this->log['url'] = $url;
        $this->log['result'] = $content;
        return (json_decode($content));
    }
    public function getLog(){
        return $this->log;
    }

    /**
     * @method generateURL
     * @param $apiCall
     * @param $parameters
     * @return string
     */
    private function generateURL($apiCall, $parameters = []) {
        $url       = self::API_URL.$apiCall;
        $conCat       = "?";
        foreach ($parameters as $key => $value) {
            $url .= !$value ?null: ($conCat.$key."=".$value);
            $conCat = "&";
        }
        return $url;
    }
}
