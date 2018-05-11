<?php

class MultiCurl extends BulkDeliverer {

    protected $handles = array();

    static public function forThenHookType() {
        return "ThenWebHook";
    }

    public function addHandle($handle) {
        $this->handles[] = $handle;
    }

    public function execute() {
        if (count($this->handles)) {
            $async_curl = curl_multi_init();
            foreach ($this->handles as $handle) {
                curl_multi_add_handle($async_curl, $handle);
            }
            $active = null;
            do {
                $mrc = curl_multi_exec($async_curl, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($async_curl) != -1) {
                    do {
                        $mrc = curl_multi_exec($async_curl, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }
            foreach ($this->handles as $handle) {
                curl_multi_remove_handle($async_curl, $handle);
            }
            curl_multi_close($async_curl);
            return "log";
        }
    }
}