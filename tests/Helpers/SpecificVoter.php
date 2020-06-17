<?php
declare(strict_types=1);

namespace Tests\Helpers;

use Voter\Voter;
use Voter\VoterUser;

class SpecificVoter implements Voter
{

    public function canVote(string $permission, $subject = null): bool
    {
        return $permission == 'specific';
    }

    public function vote(?VoterUser $user, string $permission, $subject = null): bool
    {
        return true;
    }
}