<?php
declare(strict_types=1);

namespace ThomasMiceli\Tests\Helpers;

use ThomasMiceli\Voter\VoterUser;

class TestArticle
{

    /**
     * @var VoterUser
     */
    private VoterUser $user;

    public function __construct(VoterUser $user)
    {
        $this->user = $user;
    }

    public function getAuthor(): VoterUser
    {
        return $this->user;
    }
}