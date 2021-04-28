<?php

namespace ThomasMiceli\Tests\Helpers;

use ThomasMiceli\Voter\Strategy\VoterStrategy;
use ThomasMiceli\Voter\VoterUser;

class CustomStrategy implements VoterStrategy {

    public function can(?VoterUser $user, array $voters, string $attribute, $subject): bool
    {
        $approvals = 0;
        foreach ($voters as $voter) {
            if ($voter->canVote($attribute, $subject)) {
                $vote = $voter->vote($user, $attribute, $subject);
                //ConsoleLogger::debug($voter, $vote, $attribute, $user, $subject);

                if ($vote) {
                    ++$approvals;
                }
            }
        }
        return $approvals > 3;
    }
}