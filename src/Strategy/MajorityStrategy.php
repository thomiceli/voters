<?php
declare(strict_types=1);

namespace Voter\Strategy;

use Voter\VoterUser;

/**
 * Class MajorityStrategy
 *
 * @package Voter\Strategy
 */
final class MajorityStrategy implements VoterStrategy
{

    /**
     * Call all the registered voters, and then vote when they can.
     * If the majority of the voters vote yes, the method return true.
     *
     * {@inheritDoc}
     */
    public function can(?VoterUser $user, array $voters, string $attribute, $subject): bool
    {
        $count = count($voters);
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
        return $count / 2 < $yes;
    }
}