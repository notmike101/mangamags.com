<?php
	class CacheAPC {
	    var $iTtl = 86400; // Time To Live default
	    var $bEnabled = false; // APC enabled?

	    // constructor
	    function CacheAPC($liveTime = 86400) {
	        $this->bEnabled = extension_loaded('apc');
	        $this->iTtl = $liveTime;
	    }

	    // get data from memory
	    function getData($sKey) {
	        $bRes = false;
	        $vData = apc_fetch($sKey, $bRes);
	        return ($bRes) ? $vData :null;
	    }

	    // save data to memory
	    function setData($sKey, $vData) {
	        return apc_store($sKey, $vData, $this->iTtl);
	    }

	    // delete data from memory
	    function delData($sKey) {
	        return (apc_exists($sKey)) ? apc_delete($sKey) : true;
	    }
	}
?>
