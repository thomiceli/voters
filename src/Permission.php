<?php
declare(strict_types=1);

namespace Voter;

use InvalidArgumentException;
use Voter\Strategy\AffirmativeStrategy;
use Voter\Strategy\GenericStrategy;
use Voter\Strategy\MajorityStrategy;
use Voter\Strategy\VetoStrategy;
use Voter\Strategy\VoterStrategy;

/**
 * Class Permission
 *
 * @package Voter
 */
class Permission
{

    /**
     * @var VoterStrategy
     */
    private VoterStrategy $strategy;

    /**
     * @var Voter[]
     */
    private array $voters = [];

    /**
     * Permission constructor with AffirmativeStrategy as default strategy.
     *
     * @param VoterStrategy|null $strategy
     */
    public function __construct(
        VoterStrategy $strategy = null
    ) {
        if ($strategy == null) {
            $strategy = new AffirmativeStrategy;
        }

        $this->strategy = $strategy;
    }

    /**
     * Create a Permission with AffirmativeStrategy.
     *
     * @return Permission
     */
    public static function affirmative()
    {
        return new self(new AffirmativeStrategy);
    }

    /**
     * Create a Permission with VetoStrategy.
     *
     * @return Permission
     */
    public static function veto()
    {
        return new self(new VetoStrategy);
    }

    /**
     * Create a Permission with MajorityStrategy.
     *
     * @return Permission
     */
    public static function majority()
    {
        return new self(new MajorityStrategy);
    }

    /**
     * Create a Permission with GenericStrategy.
     *
     * @param float $percentage The percentage of voters which approved the attribute.
     * @param int   $approvals  The number of voters which approved the attribute.
     *
     * @return Permission
     */
    public static function generic(float $percentage, int $approvals)
    {
        return new self(new GenericStrategy($percentage, $approvals));
    }

    /**
     * Call the method from the defined strategy.
     *
     * @param VoterUser|null $user      The user submitted to the validation.
     * @param string         $attribute The attribute to check.
     * @param null           $subject   The subject of the vote.
     *
     * @return bool
     */
    public function can(?VoterUser $user, string $attribute, $subject = null): bool
    {
        return $this->strategy->can($user, $this->voters, $attribute, $subject);
    }

    /**
     * Adds a voter.
     *
     * @param Voter $voter
     */
    public function addVoter(Voter $voter)
    {
        $this->voters[] = $voter;
    }

    /**
     * Adds one or multiple voters.
     *
     * @param Voter ...$voters
     */
    public function addVoters(Voter ...$voters)
    {
        $this->voters = [...$this->voters, ...$voters];
    }

    /**
     * Remove all the voters
     */
    public function clearVoters()
    {
        $this->voters = [];
    }

    /**
     * @return VoterStrategy
     */
    public function getStrategy(): VoterStrategy
    {
        return $this->strategy;
    }

    /**
     * @return Voter[]
     */
    public function getVoters(): array
    {
        return $this->voters;
    }

}
