<?php
declare(strict_types=1);

namespace ThomasMiceli\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ThomasMiceli\Tests\Helpers\ArticleVoter;
use ThomasMiceli\Tests\Helpers\CustomStrategy;
use ThomasMiceli\Tests\Helpers\NoVoter;
use ThomasMiceli\Tests\Helpers\SpecificVoter;
use ThomasMiceli\Tests\Helpers\TestArticle;
use ThomasMiceli\Tests\Helpers\TestVoterUser;
use ThomasMiceli\Tests\Helpers\YesVoter;
use ThomasMiceli\Voter\Permission;
use ThomasMiceli\Voter\Strategy\AffirmativeStrategy;
use ThomasMiceli\Voter\Strategy\GenericStrategy;
use ThomasMiceli\Voter\Strategy\MajorityStrategy;
use ThomasMiceli\Voter\Strategy\VetoStrategy;

class Test extends TestCase
{

    public function testEmptyVoters()
    {
        $permission = new Permission();
        $user = new TestVoterUser();
        $this->assertFalse($permission->can($user, 'test'));
    }

    public function testTrueVoter()
    {
        $permission = new Permission();
        $user = new TestVoterUser();
        $permission->addVoter(new YesVoter());
        $this->assertTrue($permission->can($user, 'test'));
    }


    public function testAddVoters()
    {
        $permission = new Permission();
        $permission->addVoters(new YesVoter(), new NoVoter());
        $permission->addVoters(new NoVoter(), new YesVoter());
        $permission->addVoter(new NoVoter());
        $this->assertCount(5, $permission->getVoters());
    }

    public function testWithOneTrueVoter()
    {
        $permission = new Permission(new AffirmativeStrategy);
        $user = new TestVoterUser();
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $this->assertTrue($permission->can($user, 'test'));

        $permission = new Permission(new AffirmativeStrategy);
        $user = new TestVoterUser();
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $this->assertFalse($permission->can($user, 'test'));
    }

    public function testWithAllTrueVoter()
    {
        $permission = new Permission(new VetoStrategy);
        $user = new TestVoterUser();
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $this->assertFalse($permission->can($user, 'test'));

        $permission = new Permission(new VetoStrategy);
        $user = new TestVoterUser();
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $this->assertTrue($permission->can($user, 'test'));
    }

    public function testWithMajorityTrueVoter()
    {
        $permission = new Permission(new MajorityStrategy);
        $user = new TestVoterUser();
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $this->assertFalse($permission->can($user, 'test'));

        $permission = new Permission(new MajorityStrategy);
        $user = new TestVoterUser();
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $this->assertTrue($permission->can($user, 'test'));
    }

    public function testWithSpecificPermissionVoter()
    {
        $permission = new Permission();
        $user = new TestVoterUser();
        $permission->addVoter(new SpecificVoter());
        $this->assertFalse($permission->can($user, 'test'));
        $this->assertTrue($permission->can($user, 'specific'));
    }

    public function testWithConditionVoter()
    {
        $permission = new \ThomasMiceli\Voter\Permission();
        $user = new TestVoterUser();
        $user2 = new TestVoterUser();
        $user3 = null; // disconnected user
        $post = new TestArticle($user);

        $permission->addVoter(new ArticleVoter());
        $this->assertTrue($permission->can($user, ArticleVoter::VIEW, $post));
        $this->assertTrue($permission->can($user, ArticleVoter::EDIT, $post));
        $this->assertTrue($permission->can($user2, ArticleVoter::VIEW, $post));
        $this->assertFalse($permission->can($user2, ArticleVoter::EDIT, $post));
        $this->assertFalse($permission->can($user3, ArticleVoter::VIEW, $post));
        $this->assertFalse($permission->can($user3, ArticleVoter::EDIT, $post));
    }

    public function testPermissionFactories()
    {
        $permission1 = Permission::affirmative();
        $permission2 = Permission::veto();
        $permission3 = Permission::majority();
        $permission4 = Permission::generic(50, 0);

        $this->assertInstanceOf(AffirmativeStrategy::class, $permission1->getStrategy());
        $this->assertInstanceOf(VetoStrategy::class, $permission2->getStrategy());
        $this->assertInstanceOf(MajorityStrategy::class, $permission3->getStrategy());
        $this->assertInstanceOf(GenericStrategy::class, $permission4->getStrategy());
    }

    public function testCustomStrategy()
    {
        $permission = new Permission(new CustomStrategy);
        $user = new TestVoterUser();

        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());

        $this->assertTrue($permission->can($user, 'test'));


        $permission = new Permission(new CustomStrategy);
        $user = new TestVoterUser();

        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());

        $this->assertFalse($permission->can($user, 'test'));
    }

    public function testGenericStrategy()
    {
        $permission = new Permission(new GenericStrategy(0, 3));
        $user = new TestVoterUser();

        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $this->assertTrue($permission->can($user, 'test'));
        $permission->clearVoters();

        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $this->assertFalse($permission->can($user, 'test'));


        $permission = new Permission(new GenericStrategy(62.5, 0));

        // 5/7 = 0.7143
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $this->assertTrue($permission->can($user, 'test'));
        $permission->clearVoters();

        // 5/8 = 0.625
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $this->assertFalse($permission->can($user, 'test'));


        $permission = new Permission(new GenericStrategy(40, 5));

        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $this->assertFalse($permission->can($user, 'test'));
        $permission->clearVoters();

        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new NoVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new YesVoter());
        $permission->addVoter(new NoVoter());
        $this->assertFalse($permission->can($user, 'test'));

    }

    public function testGenericStrategyException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GenericStrategy should either have a valid percentage or a number of approvals defined.');
        $permission = new Permission(new GenericStrategy(0, 0));

    }

}