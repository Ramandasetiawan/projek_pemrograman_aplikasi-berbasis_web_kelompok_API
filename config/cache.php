<?php
/**
 * Performance Optimization Helpers
 * Cache helper functions untuk meningkatkan performa
 */

/**
 * Simple file-based cache
 */
class SimpleCache {
    private $cache_dir;
    private $cache_time;
    
    public function __construct($cache_dir = '../cache/', $cache_time = 300) {
        $this->cache_dir = $cache_dir;
        $this->cache_time = $cache_time; // 5 minutes default
        
        // Create cache directory if not exists
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }
    
    /**
     * Get cached data
     */
    public function get($key) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        
        if (!file_exists($cache_file)) {
            return null;
        }
        
        // Check if cache expired
        if (time() - filemtime($cache_file) > $this->cache_time) {
            unlink($cache_file);
            return null;
        }
        
        $data = file_get_contents($cache_file);
        return unserialize($data);
    }
    
    /**
     * Set cache data
     */
    public function set($key, $data) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        $serialized = serialize($data);
        return file_put_contents($cache_file, $serialized);
    }
    
    /**
     * Delete cache
     */
    public function delete($key) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        if (file_exists($cache_file)) {
            return unlink($cache_file);
        }
        return false;
    }
    
    /**
     * Clear all cache
     */
    public function clear() {
        $files = glob($this->cache_dir . '*.cache');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }
}

/**
 * Get or set cached data with callback
 */
function cache_remember($key, $callback, $cache_time = 300) {
    $cache = new SimpleCache('../cache/', $cache_time);
    
    $data = $cache->get($key);
    
    if ($data === null) {
        $data = $callback();
        $cache->set($key, $data);
    }
    
    return $data;
}

/**
 * Invalidate cache by key pattern
 */
function cache_invalidate($pattern) {
    $cache = new SimpleCache();
    $cache->delete($pattern);
}

/**
 * Clear all cache
 */
function cache_clear_all() {
    $cache = new SimpleCache();
    return $cache->clear();
}
?>
