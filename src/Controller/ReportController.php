<?php

namespace App\Controller;

use App\Service\ProjectService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends AbstractController
{
    public function __construct(private readonly ProjectService $projectService)
    {
    }

    #[Route('/app/evaluation', name: 'metrics')]
    public function index(Request $request): Response
    {
        return $this->render('metrics/index.html.twig');
    }

    #[Route('/app/evaluation/daily', name: 'daily_metrics')]
    public function dailyEvaluation(Request $request): Response
    {
        $startDate = DateTime::createFromFormat("Y-m-d\TH:i", $request->query->get('startDate'));
        $endDate = DateTime::createFromFormat("Y-m-d\TH:i", $request->query->get('endDate'));
        return new JsonResponse($this->projectService->getProjectsDailyReport($this->getUser(), $startDate, $endDate));
    }

    #[Route('/app/evaluation/monthly', name: 'monthly_metrics')]
    public function monthlyEvaluation(Request $request): Response
    {
        $startDate = DateTime::createFromFormat("Y-m-d\TH:i", $request->query->get('startDate'));
        $endDate = DateTime::createFromFormat("Y-m-d\TH:i", $request->query->get('endDate'));
        return new JsonResponse($this->projectService->getProjectsMonthlyReport($this->getUser(), $startDate, $endDate));
    }

    #[Route('/app/evaluation/csv', name: 'csv_report')]
    public function csvReport(): Response
    {
        $csvWriter = $this->projectService->getProjectsReport($this->getUser());

        // Set the HTTP response headers
        $response = new StreamedResponse(function () use ($csvWriter) {
            $outputStream = fopen('php://output', 'w');
            fwrite($outputStream, $csvWriter->toString());
            fclose($outputStream);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="report.csv"');

        return $response;
    }
}
