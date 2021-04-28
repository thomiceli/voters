<?php
declare(strict_types=1);

namespace ThomasMiceli\Tests\Helpers;

use ThomasMiceli\Voter\Voter;
use ThomasMiceli\Voter\VoterUser;

class ArticleVoter implements Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';

    public function canVote(string $attribute, $subject = null): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof TestArticle;
    }

    public function vote(?VoterUser $user, string $attribute, $subject = null): bool
    {
        /** @var TestArticle $subject */
        switch ($attribute) {
            case self::VIEW: return $user !== null;
            case self::EDIT: return $subject->getAuthor() === $user;
        }

        return false;
    }
}