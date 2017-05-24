<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 12:38 PM
 */

namespace Trimm\Jelastic\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Cache
{
    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter(
        // the subdirectory of the main cache directory where cache items are stored
            'jelastic',
            // in seconds; applied to cache items that don't define their own lifetime
            // 0 means to store the cache items indefinitely (i.e. until the files are deleted)
            $defaultLifetime = 0,
            // the main cache directory (the application needs read-write permissions on it)
            // if none is specified, a directory is created inside the system temporary directory
            $directory = null
        );
    }

    /**
     * @return FilesystemAdapter
     */
    public function getAdapter() {
        return $this->cache;
    }

}