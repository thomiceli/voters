# Voters

Useful to check complex user permissions.
###### Inspired from [Symfony voter system](https://symfony.com/doc/current/security/voters.html). 

### Installation

```shell
$ composer require thomas-miceli/voters
```

### Usage

To check a specific permission of an user action `$attribute` (e.g. view or edit) with or without a `$subject` (e.g. an article), every registered voters will be called and will be checked to see if they can vote for the permission.
Every eligible voters will vote whether the user can have the permission or not.

Let's setup permissions for our blog.

#### Create the Permission object

```php
<?php 

$permission = new \ThomasMiceli\Voter\Permission;
$permission->addVoter(new ArticleVoter); // register the voter for this permission object
$user = ...
$article = ...

$permission->can($user, ArticleVoter::VIEW, $article); // check if our user can view the article
$permission->can($user, ArticleVoter::EDIT, $article); // check if our user can edit the article
```

#### Create the voter

```php
<?php

/* This voter determine what the user can do with an article from our blog. */
class ArticleVoter implements \ThomasMiceli\Voter\Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';

    // if the voter can vote for the requested attribute...
    public function canVote(string $attribute, $subject = null): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof Article;
    }

    // ...if yes, the voter will determinate and return the permission for this attribute
    public function vote(?\ThomasMiceli\Voter\VoterUser $user, string $attribute, $subject = null): bool
    {
        /** @var Article $subject */
        switch ($attribute) {
            // if the user is connected, he can read the article
            case self::VIEW: return $user !== null; 
            
            // if the user is the author, he can modify the article
            case self::EDIT: return $subject->getAuthor() === $user;
        }
        
        // the user has not the permission for other attributes
        return false;
    }
}
```

### Strategies

It's possible to set different strategies to determine the permission when multiple voters are eligible to vote for an attribute.

```php
<?php

$permission = new \ThomasMiceli\Voter\Permission(new \ThomasMiceli\Voter\Strategy\AffirmativeStrategy); // default strategy
// $permission::can() returns true if at least one of the registered voters approved the attribute

$permission = new \ThomasMiceli\Voter\Permission(new \ThomasMiceli\Voter\Strategy\VetoStrategy);
// $permission::can() returns true if all the registered voters approved the attribute

$permission = new \ThomasMiceli\Voter\Permission(new \ThomasMiceli\Voter\Strategy\MajorityStrategy);
// $permission::can() returns true if at least half plus one of the registered voters approved the attribute
```

We can use factory static methods for a better readability.

```php
<?php

$permission = \ThomasMiceli\Voter\Permission::affirmative();
$permission = \ThomasMiceli\Voter\Permission::veto();
$permission = \ThomasMiceli\Voter\Permission::majority();
```

We can create our own strategy and set it to a permission later...

```php
<?php

class CustomStrategy implements \ThomasMiceli\Voter\Strategy\VoterStrategy {
    
    // the permission is granted if at least 4 voters voted true for an attribute
    public function can(?\ThomasMiceli\Voter\VoterUser $user, array $voters, string $attribute, $subject): bool
    {
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
        return $approvals > 3;
    }
}
```

...then use it.

```php
<?php

$permission = new \ThomasMiceli\Voter\Permission(new CustomStrategy);
```

We can even easily create generics voters which allows to define how many voters should approve the attribute.

```php
<?php

$permission = new \ThomasMiceli\Voter\Permission(new \ThomasMiceli\Voter\Strategy\GenericStrategy(40, 5));
$permission = \ThomasMiceli\Voter\Permission::generic(40, 5);
// at least 40% and 5 voters should have approved the attribute 
```

