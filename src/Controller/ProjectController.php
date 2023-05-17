<?php
namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\ProjectService;
use App\Service\TimeLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProjectController extends AbstractController
{

    public function __construct(
        private readonly ProjectService $projectService,
        private readonly TimeLogService $timeLogService
    )
    {
    }

    #[Route('/app/projects', name: 'project_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $this->projectService->getProjectsByUser($this->getUser()),
        ]);
    }

    #[Route('/app/project/create', name: 'project_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->createProject($project, $this->getUser());

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/app/project/{id}', name: 'project_show', methods: ['GET'])]
    #[IsGranted('view', subject: 'project')]
    public function show(Project $project): Response
    {

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'timeLogs' => $this->timeLogService->getByProject($project)
        ]);
    }
}