<?php
declare(strict_types=1);

namespace ThomasMiceli\Voter\Strategy;

use ThomasMiceli\Voter\VoterUser;

/**
 * Interface VoterStrategy
 *
 * @package Voter
 */
interface VoterStrategy
{

    /**
     * @param VoterUser|null $user      The user submitted to the validation.
     * @param array          $voters    All the registered voters from the permission.
     * @param string         $attribute The attribute to check.
     * @param mixed          $subject   The subject of the vote.
     *
     * @return bool If the user has the permission.
     */
    public function can(?VoterUser $user, array $voters, string $attribute, $subject): bool;
}