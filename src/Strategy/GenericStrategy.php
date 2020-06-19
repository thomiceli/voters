<?php
declare(strict_types=1);

namespace Voter\Strategy;

use InvalidArgumentException;
use Voter\VoterUser;

/**
 * Class GenericStrategy
 *
 * @package Voter\Strategy
 */
final class GenericStrategy implements VoterStrategy
{

    /**
     * @var float
     */
    private float $percentage;

    /**
     * @var int
     */
    private int $approvals;

    /**
     * GenericStrategy constructor. Allows to define how many voters
     * should approve the attribute.
     *
     * If a parameter is set to 0, the strategy will not count it to
     * calculate the permission, but at least one parameter should be
     * set other than 0.
     *
     * @param float $percentage The percentage of voters which approved the attribute.
     * @param int   $approvals  The number of voters which approved the attribute.
     */
    public function __construct(float $percentage, int $approvals)
    {
        if (($percentage === 0.0 && $approvals === 0) || ($percentage < 0 || $percentage > 100)) {
            throw new InvalidArgumentException('GenericStrategy should either have a valid percentage or a number of approvals defined.');
        }

        $this->percentage = $percentage;
        $this->approvals = $approvals;
    }

    /**
     * Call all the registered voters, and then vote when they can.
     * If the majority of the voters approved the attribute, the method return true.
     *
     * {@inheritDoc}
     */
    public function can(?VoterUser $user, array $voters, string $attribute, $subject): bool
    {
        $count = count($voters);
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

        $percentage = ($approvals / $count) * 100;
        $percentageBool = $this->percentage == 0 ? true : $percentage > $this->percentage;
        $approvalsBool = $this->approvals == 0 ? true : $approvals > $this->approvals;

        return $percentageBool && $approvalsBool;
    }
}