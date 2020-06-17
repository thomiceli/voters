<?php

namespace Tests\Helpers;

use Voter\Strategy\VoterStrategy;
use Voter\VoterUser;

class CustomStrategy implements VoterStrategy {

    public function can(?VoterUser $user, array $voters, string $attribute, $subject): bool
    {
        $yes = 0;
        foreach ($voters as $voter) {
            if ($voter->canVote($attribute, $subject)) {
                $vote = $voter->vote($user, $attribute, $subject);
                //ConsoleLogger::debug($voter, $vote, $attribute, $user, $subject);

                if ($vote) {
                    ++$yes;
                }
            }
        }
        return $yes > 3;
    }
}