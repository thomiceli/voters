<?php
declare(strict_types=1);

namespace Tests\Helpers;

use Voter\VoterUser;

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