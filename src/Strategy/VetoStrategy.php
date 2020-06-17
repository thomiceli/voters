<?php
declare(strict_types=1);

namespace Voter\Strategy;

use Voter\VoterUser;

/**
 * Class VetoStrategy
 *
 * @package Voter\Strategy
 */
final class VetoStrategy implements VoterStrategy
{

    /**
     * Call all the registered voters, and then vote when they can.
     * If all voters vote yes, the method return true.
     *
     * {@inheritDoc}
     */
    public function can(?VoterUser $user, array $voters, string $attribute, $subject): bool
    {
        foreach ($voters as $voter) {
            if ($voter->canVote($attribute, $subject)) {
                $vote = $voter->vote($user, $attribute, $subject);
                //ConsoleLogger::debug($voter, $vote, $attribute, $user, $subject);

                if (!$vote) {
                    return false;
                }
            }
        }
        return true;
    }
}