<?php

namespace Grace\Domain;

class Repo extends BaseDomain
{
    protected $hookPost;
    protected $cloned;
    protected $cwd;

    public static function from($changeSet)
    {

    }

    public static function fromHook(HookPost $hookPost)
    {
        $repo = new self;
        $repo->hookPost = $hookPost;
        $repo->cloned = false;
        $repo->cwd = null;

        return $repo;
    }

    /**
     * @return HookPost
     */
    public function getHookPost()
    {
        return $this->hookPost;
    }

    public function getBaseUrl()
    {
        return 'github.com';
    }

    public function isCloned()
    {
        return $this->cloned;
    }

    public function wasClonedIn($cwd)
    {
        $this->cloned = true;
        $this->cwd = $cwd;
    }
}
