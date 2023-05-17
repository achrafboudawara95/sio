<?php
namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Service\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{

    public function __construct(private readonly ProjectService $projectService)
    {
    }

    #[Route('/app/projects', name: 'project_index')]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $this->projectService->getProjectsByUser($this->getUser()),
        ]);
    }
}