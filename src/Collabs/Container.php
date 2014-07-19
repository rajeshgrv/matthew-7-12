<?php

namespace Grace\Collabs;

use Grace\Domain\Repo;
use Symfony\Component\Filesystem\Filesystem;

class Container
{
    protected $helper;
    protected $basePath;
    protected $fs;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
        $this->basePath = '/tmp/grace/';
        $this->fs = new FileSystem();
        $this->fs->mkdir($this->basePath);
    }

    public function gitClone(Repo $repo)
    {
        $this->fs->mkdir($this->basePath.$repo->getCwd());
        $this->helper->run(
            sprintf(
                'git clone git@%s:%s/%s.git .',
                $repo->getBaseUrl(),
                $repo->getHookPost()->getVendor(),
                $repo->getHookPost()->getName()
            ),
            $this->basePath.$repo->getCwd()
        );

        $repo->wasCloned();
    }

    public function checkout(Repo $repo, $reference)
    {
        $this->helper->run(
            sprintf(
                'git checkout -b %s -f',
                $reference
            ),
            $this->basePath.$repo->getCwd()
        );
    }

    public function formatPatch(Repo $repo, $from)
    {
        return 'filename';
    }

    public function destroy(Repo $repo)
    {
        $this->fs->remove($this->basePath.$repo->getCwd());
    }
}