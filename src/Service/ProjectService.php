<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ProjectRepository;

class ProjectService
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @return []Project
     */
    public function getProjectsByUser(User $user): array
    {
        return $this->projectRepository->findBy(['user' => $user]);
    }
}