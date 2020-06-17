<?php
declare(strict_types=1);

namespace Tests\Helpers;

use Voter\Voter;
use Voter\VoterUser;

class NoVoter implements Voter
{

    public function canVote(string $permission, $subject = null): bool
    {
        return true;
    }

    public function vote(?VoterUser $user, string $permission, $subject = null): bool
    {
        return false;
    }
}