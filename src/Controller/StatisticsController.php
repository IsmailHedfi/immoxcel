<?php

namespace App\Controller;

use App\Repository\EmployeesRepository;
use App\Repository\LeavesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics')]
   /*
    public function index(EmployeesRepository $eR): Response
    {
        // Fetch data for number of employees hired each month
        $hiringData = $eR->getHiringData();

        // Process data to format it for Chart.js
        $labels = [];
        $data = [];
        foreach ($hiringData as $entry) {
            $labels[] = $entry['month'];
            $data[] = $entry['count'];
        }

        return $this->render('statistics/index.html.twig', [
            'labels' => json_encode($labels),
            'data' => json_encode($data),
        ]);
    }
*/

    public function statistics(LeavesRepository $lR): Response
    {
        // Fetch data for number of leaves taken each month, paginated by year
        $leaveData = $lR->getLeaveDataByYear();
var_dump($leaveData);
        // Process data to format it for Chart.js
        $years = [];
        $labels = [];
        $data = [];
        var_dump("hniii");
        foreach ($leaveData as $entry) {
            var_dump("hniii");
die();
            var_dump($entry['year']);
            $years[] = $entry['year'];
            var_dump($entry['month']);
            $labels[] = $entry['month'];
            var_dump($entry['count']);
            $data[] = $entry['count'];
        }
die();
        return $this->render('statistics/index.html.twig', [
            'years' => json_encode($years),
            'labels' => json_encode($labels),
            'data' => json_encode($data),
        ]);
    }
}
