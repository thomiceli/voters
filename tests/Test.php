<?php
declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\ArticleVoter;
use Tests\Helpers\CustomStrategy;
use Tests\Helpers\NoVoter;
use Tests\Helpers\SpecificVoter;
use Tests\Helpers\TestArticle;
use Tests\Helpers\TestVoterUser;
use Tests\Helpers\YesVoter;
use Voter\Permission;
use Voter\Strategy\AffirmativeStrategy;
use Voter\Strategy\MajorityStrategy;
use Voter\Strategy\VetoStrategy;

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
        $permission = new \Voter\Permission();
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

    public function testWrongPermissionStrategy()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid strategy');
        $permission = new Permission('iji');
    }

    public function testPermissionFactories()
    {
        $permission1 = Permission::affirmative();
        $permission2 = Permission::veto();
        $permission3 = Permission::majority();

        $this->assertInstanceOf(AffirmativeStrategy::class, $permission1->getStrategy());
        $this->assertInstanceOf(VetoStrategy::class, $permission2->getStrategy());
        $this->assertInstanceOf(MajorityStrategy::class, $permission3->getStrategy());
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

}