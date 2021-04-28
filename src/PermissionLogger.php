<?php
declare(strict_types=1);

namespace ThomasMiceli\Voter;

/**
 * Interface PermissionLogger
 *
 * @package Voter
 */
interface PermissionLogger
{
    /**
     * Outputs the current voter vote
     *
     * @param Voter     $voter
     * @param bool      $vote
     * @param string    $attribute
     * @param VoterUser $user
     * @param $subject
     *
     * @return mixed
     */
    public static function debug(Voter $voter, bool $vote, string $attribute, VoterUser $user, $subject);
}