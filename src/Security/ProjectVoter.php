<?php

namespace App\Security;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ProjectVoter implements VoterInterface
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const CREATE_TIME_LOG = 'create_timeLog';

    const SUPPORT_ATTRIBUTES = [
        self::VIEW,
        self::CREATE_TIME_LOG
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, self::SUPPORT_ATTRIBUTES)) {
            return false;
        }

        // only vote on `Project` objects
        if (!$subject instanceof Project) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, Project $project, User $user): bool
    {
        return match($attribute) {
            self::VIEW, self::CREATE_TIME_LOG => $this->canView($project, $user),
            default => throw new \LogicException(sprintf('There is no action for %s', $attribute))
        };
    }

    private function canView(Project $project, User $user): bool
    {
        // this assumes that the Project object has a `getOwner()` method
        return $user === $project->getUser();
    }

    public function vote(TokenInterface $token, mixed $subject, array $attributes)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Project object, thanks to `supports()`
        /** @var Project $project */
        $project = $subject;

        foreach ($attributes as $attribute)
        {
            if (false === $this->voteOnAttribute($attribute, $project, $user))
            {
                return false;
            }
        }

        return true;
    }
}