<?php
declare(strict_types=1);

namespace ThomasMiceli\Tests\Helpers;

use ThomasMiceli\Voter\Voter;
use ThomasMiceli\Voter\VoterUser;

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