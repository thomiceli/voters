<?php
declare(strict_types=1);

namespace ThomasMiceli\Voter;

/**
 * Interface Voter
 *
 * @package Voter
 */
interface Voter
{

    /**
     * Check if the current voter can vote.
     *
     * @param string     $attribute The attribute to check.
     * @param mixed|null $subject   The subject of the vote.
     *
     * @return bool Whether the voter eligible to vote for the attribute.
     */
    public function canVote(string $attribute, $subject = null) : bool;

    /**
     * Return the result of the vote.
     *
     * @param VoterUser|null $user      The user submitted to the validation.
     * @param string         $attribute The attribute to check.
     * @param mixed|null     $subject   The subject of the vote.
     *
     * @return bool Whether the user has the permission.
     */
    public function vote(?VoterUser $user, string $attribute, $subject = null) : bool;
}
