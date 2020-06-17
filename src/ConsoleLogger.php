<?php
declare(strict_types=1);

namespace Voter;

/**
 * Class ConsoleLogger
 *
 * @package Voter
 */
final class ConsoleLogger implements PermissionLogger
{

    /**
     * {@inheritDoc}
     */
    public static function debug(Voter $voter, bool $vote, string $attribute, VoterUser $user, $subject)
    {
        $className = get_class($voter);
        $vote = $vote ? "\e[32myes\e[0m" : "\e[31mno\e[0m";

        file_put_contents('php://stdout', "\n\e[34m$className\e[0m : $vote on \e[36m$attribute\e[0m\n");
    }
}