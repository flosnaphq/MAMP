<?php

class CacheMemcache
{
    private $objMemcached = null;

	const CONF_MEMCACHE_HOST = '112.196.9.21';
	const CONF_MEMCACHE_PORT = 11211;
	const CONF_MEMCACHE_WEIGHT = 1;

    const CONF_MEMCACHE_EXPIRATION_TIME = 30; // max 30
	public $cache = array();
	
	function __construct() {
        $this->objMemcached = new Memcache();
		$this->objMemcached->addServer(static::CONF_MEMCACHE_HOST, static::CONF_MEMCACHE_PORT, static::CONF_MEMCACHE_WEIGHT);
    }
	
    /* Add to cache if not exist. */
    function add( $key, $data, $expire = 0 )
	{
        $expire = ( $expire == 0 ) ? static::CONF_MEMCACHE_EXPIRATION_TIME : $expire;
        $result  = $this->objMemcached->add($key, array($data, time(), $expire), 0, $expire);
        if ( false !== $result ) {
            $this->cache[$key] = $data;
        }

        return $result;
    }

    /* Remove cache key */
    function delete($key) {

        $result = $this->objMemcached->delete($key);
        if ( false !== $result ) {
            unset( $this->cache[$key] );
        }
        return $result;
    }

    /* Clears the object cache of all data */
    function flush() {
        $this->cache = array ();
        return $this->objMemcached->flush();
    }

    /* Retrieves the cache contents, if it exists */
    function get( $key )
	{
		$found = false;
        if ( $this->isKeyExists($key) ) {
			$found = true;
            $value = $this->cache[$key];
        } else {
			$found = true;
			$value = $this->objMemcached->get($key);
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
        $expiryTime = ( $expire == 0 ) ? static::CONF_MEMCACHE_EXPIRATION_TIME : $expire;

        $result = $this->objMemcached->set($key, $store_data, 0, $expire);

        return $result;
    }

    /* Utility function to determine whether a key exists in the cache. */
    protected function isKeyExists( $key ) {
        return isset( $this->cache[ $key ] );
    }

    /** Check whether Memcache is available. */
    public static function isSupported() {
        if ( !class_exists('Memcache') ) {
            error_log('The Memcached Extension must be loaded to use Memcached Cache.');
            return false;
        }
        return true;
    }

}