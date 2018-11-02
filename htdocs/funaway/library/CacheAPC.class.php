<?php

class CacheAPC
{
    const CONF_APC_EXPIRATION_TIME = 30; // max 30
	public $cache = array();
	public $apcSitePrefix = CONF_WEBROOT_URL;
	
	function __construct()
	{
		$this->apcSitePrefix = FatUtility::generateFullUrl('','', array(), CONF_WEBROOT_URL);
    }
	
    /* Add to cache if not exist. */
    function add( $key, $data, $expireTime = 0 )
	{
		$key = $this->formatKey($key);
        $expireTime = ( $expireTime == 0 ) ? static::CONF_EXPIRATION_TIME : $expireTime;
        $result  = apc_add( $key, $data, $expireTime );
        if ( false !== $result ) {
            $this->cache[$key] = $data;
        }

        return $result;
    }

    /* Remove cache key */
    function delete($key)
	{
		$key = $this->formatKey($key);
        
		$result = apc_delete( $key );
		
        if ( false !== $result ) {
            unset( $this->cache[$key] );
        }
        return $result;
    }

    /* Clears the object cache of all data */
    function flush()
	{
        $this->cache = array ();
		if (extension_loaded('apcu')) {
            return apc_clear_cache();
        } else {
            return apc_clear_cache('user');
        }
    }

    /* Retrieves the cache contents, if it exists */
    function get( $key )
	{
		$found = false;
        if ( $this->isCacheKeyExists($key) ) {
			$found = true;
            $value = $this->cache[$key];
        } else {
			$found = true;
			$value = apc_fetch( $key);
		}
		
		if ( null === $value ) {
			$value = false;
			$found = false;
		}
		var_dump($found); exit;
		if(true === $found) {
			$return = $this->cache[$key];
		} else {
			$return = false;
		}
		
        return $return;
    }

    /* Set cache */
    function set($key, $data, $expiryTime = 0)
	{
		$key = $this->formatKey($key);
		
        $expiryTime = ( $expiryTime == 0 ) ? static::CONF_EXPIRATION_TIME : $expiryTime;

		$this->cache[$key] = $data;
		
        $result = apc_store($key, $store_data, $expiryTime);

        return $result;
    }

    /* Whether a key exists in the cache. */
    protected function isCacheKeyExists( $key )
	{
		$key = $this->formatKey($key);
		return isset( $this->cache[ $key ] );
    }

	private function formatKey($key)
	{
		return $this->apcSitePrefix . $key;
	}
    /** Check whether APC is available. */
    public static function isSupported()
	{
        if ( ! extension_loaded('apc') OR ini_get('apc.enabled') != "1") {
            return false;
        }
        return true;
    }

}