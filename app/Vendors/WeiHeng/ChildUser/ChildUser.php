<?php

namespace WeiHeng\ChildUser;


use Illuminate\Contracts\Cache\Repository as Cache;
use App\Models\ChildUser as User;

class ChildUser
{
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * 缓存
     *
     * @param \App\Models\ChildUser $childUser
     * @param bool $force
     * @return mixed
     */
    public function cacheNodes(User $childUser, $force = false)
    {
        $key = $this->getCacheName($childUser);
        if (!$force && $this->cache->has($key)) {
            return $this->cache->get($key);
        }
        $nodes = $childUser->indexNodes;
        $this->cache->forever($key, $nodes);
        return $nodes;

    }

    /**
     * 缓存
     *
     * @param \App\Models\ChildUser $childUser
     * @return string
     */
    public function getCacheName(User $childUser)
    {
        return 'child-user:nodes:' . $childUser->id;
    }
}