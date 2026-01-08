<?php

class SimpleCache {
    private $cache_dir;
    private $cache_time;

    public function __construct($cache_dir = '../cache/', $cache_time = 300) {
        $this->cache_dir = $cache_dir;
        $this->cache_time = $cache_time; // 5 minutes default

        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }

    public function get($key) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';

        if (!file_exists($cache_file)) {
            return null;
        }

        if (time() - filemtime($cache_file) > $this->cache_time) {
            unlink($cache_file);
            return null;
        }

        $data = file_get_contents($cache_file);
        return unserialize($data);
    }

    public function set($key, $data) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        $serialized = serialize($data);
        return file_put_contents($cache_file, $serialized);
    }

    public function delete($key) {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        if (file_exists($cache_file)) {
            return unlink($cache_file);
        }
        return false;
    }

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

function cache_remember($key, $callback, $cache_time = 300) {
    $cache = new SimpleCache('../cache/', $cache_time);

    $data = $cache->get($key);

    if ($data === null) {
        $data = $callback();
        $cache->set($key, $data);
    }

    return $data;
}

function cache_invalidate($pattern) {
    $cache = new SimpleCache();
    $cache->delete($pattern);
}

function cache_clear_all() {
    $cache = new SimpleCache();
    return $cache->clear();
}
?>
