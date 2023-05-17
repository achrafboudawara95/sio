<?php
namespace App\Controller;

use App\Entity\Project;
use App\Entity\TimeLog;
use App\Exception\ValidationException;
use App\Form\TimeLogType;
use App\Service\TimeLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TimeLogController extends AbstractController
{

    public function __construct(private readonly TimeLogService $timeLogService)
    {
    }

    #[Route('/project/{id}/time-log/create', name: 'time_log_create')]
    #[IsGranted('create_timeLog', subject: 'project')]
    public function create(Request $request, Project $project): Response
    {
        $timeLog = new TimeLog();

        $form = $this->createForm(TimeLogType::class, $timeLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->timeLogService->createTimeLog($project, $timeLog);

                return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
            } catch (ValidationException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('timelog/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }
}