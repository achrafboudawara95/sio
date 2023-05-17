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

    #[Route('/project/{id}/time-log/create', name: 'time_log_create', methods: ['GET', 'POST'])]
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
                //TODO: replace with exception handler
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('timelog/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/timelog/delete/{id}', name: 'timelog_delete', methods: ['POST'])]
    public function delete(TimeLog $timeLog): Response
    {
        $this->timeLogService->delete($timeLog);

        return $this->redirectToRoute('project_show', ['id' => $timeLog->getProject()->getId()]);
    }

    #[Route('/project/{id}/time-log/start', name: 'time_log_start', methods: ['GET'])]
    #[IsGranted('create_timeLog', subject: 'project')]
    public function start(Project $project): Response
    {
        try {
            $this->timeLogService->start($project);
        } catch (ValidationException $e) {
            //TODO: replace with exception handler
        }

        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
    }

    #[Route('/timelog/stop/{id}', name: 'time_log_stop', methods: ['GET'])]
    public function stop(TimeLog $timeLog): Response
    {
        $this->timeLogService->stop($timeLog);

        return $this->redirectToRoute('project_show', ['id' => $timeLog->getProject()->getId()]);
    }
}