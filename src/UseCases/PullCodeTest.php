<?php

namespace Grace\UseCases;

use Grace\Domain\GithubPost;
use Grace\Domain\Repo;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class PullCodeTest extends WebTestCase
{
    protected $container;
    protected $puller;
    protected $differ;
    protected $subscriber;
    protected $mailer;
    protected $zipper;

    public function setUp()
    {
        $this->createClient();
        $container = static::$kernel->getContainer();
        $this->puller = $container->get('grace.puller');
        $this->differ = $container->get('grace.differ');
        $this->subscriber = $container->get('grace.subscriber');
        $this->mailer = $container->get('grace.mailer');
        $this->zipper = $container->get('grace.zipper');
    }

    /**
     * @test
     * @dataProvider getRequestExamples
     */
    public function it_goes_through_the_whole_pull_flow(Request $request)
    {
        $hookPost = GithubPost::fromRequest($request);
        $repo = $this->puller->__invoke(Repo::fromHook($hookPost));
        $patch = $this->differ->__invoke($repo);
        $manyCompressed = $this->zipper->__invoke($patch);
        $list = $this->subscriber->__invoke($repo);
        $this->mailer->__invoke($list, $manyCompressed);
        $this->container->destroy();
    }

    public function getRequestExamples()
    {
        return [
            [$this->getRequest(file_get_contents(__DIR__.'/Fixtures/request.json'))],
            [$this->getRequest(file_get_contents(__DIR__.'/Fixtures/request.json'))],
        ];
    }

    private function getRequest($content)
    {
        return new Request([], [], [], [], [], [], json_decode($content, true));
    }

    protected static function getKernelClass()
    {
        return 'Grace\\AppKernel';
    }
}
