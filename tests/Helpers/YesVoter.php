<?php
declare(strict_types=1);

namespace ThomasMiceli\Tests\Helpers;

use ThomasMiceli\Voter\Voter;
use ThomasMiceli\Voter\VoterUser;

class YesVoter implements Voter
{

    public function canVote(string $permission, $subject = null): bool
    {
        return true;
    }

    public function vote(?VoterUser $user, string $permission, $subject = null): bool
    {
        return true;
    }
}