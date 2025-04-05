<?php
/**
 * BRMedia Cache Class
 *
 * Manages caching for frontend assets and data.
 *
 * @package BRMedia\Includes\Frontend
 */

namespace BRMedia\Includes\Frontend;

class BRMedia_Cache {
    /** @var string Cache type (transient, redis, memcached) */
    private $cache_type;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cache_type = $this->detect_cache_type();
        add_action('wp_loaded', [$this, 'init_cache']);
    }

    /**
     * Detects available cache type
     *
     * @return string Cache type
     */
    private function detect_cache_type() {
        if (class_exists('Redis') && defined('BRMEDIA_REDIS_HOST')) {
            return 'redis';
        } elseif (class_exists('Memcached') && defined('BRMEDIA_MEMCACHED_HOST')) {
            return 'memcached';
        }
        return 'transient';
    }

    /**
     * Initializes caching
     */
    public function init_cache() {
        // Example: Cache player data
        if (!$this->get('player_data')) {
            $data = $this->generate_player_data();
            $this->set('player_data', $data, DAY_IN_SECONDS);
        }
    }

    /**
     * Generates player data (placeholder)
     *
     * @return array Player data
     */
    private function generate_player_data() {
        return ['example' => 'data']; // Replace with actual data generation
    }

    /**
     * Gets cached data
     *
     * @param string $key Cache key
     * @return mixed Cached data
     */
    public function get($key) {
        switch ($this->cache_type) {
            case 'redis':
                $redis = new \Redis();
                $redis->connect(BRMEDIA_REDIS_HOST, BRMEDIA_REDIS_PORT);
                return $redis->get("brmedia:$key");
            case 'memcached':
                $memcached = new \Memcached();
                $memcached->addServer(BRMEDIA_MEMCACHED_HOST, BRMEDIA_MEMCACHED_PORT);
                return $memcached->get("brmedia:$key");
            default:
                return get_transient("brmedia_$key");
        }
    }

    /**
     * Sets cached data
     *
     * @param string $key Cache key
     * @param mixed $data Data to cache
     * @param int $expiration Expiration time in seconds
     */
    public function set($key, $data, $expiration) {
        switch ($this->cache_type) {
            case 'redis':
                $redis = new \Redis();
                $redis->connect(BRMEDIA_REDIS_HOST, BRMEDIA_REDIS_PORT);
                $redis->setex("brmedia:$key", $expiration, serialize($data));
                break;
            case 'memcached':
                $memcached = new \Memcached();
                $memcached->addServer(BRMEDIA_MEMCACHED_HOST, BRMEDIA_MEMCACHED_PORT);
                $memcached->set("brmedia:$key", $data, $expiration);
                break;
            default:
                set_transient("brmedia_$key", $data, $expiration);
                break;
        }
    }
}