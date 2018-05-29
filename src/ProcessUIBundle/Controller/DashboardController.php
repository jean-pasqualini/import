<?php

namespace Darkilliant\ProcessUIBundle\Controller;

use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessUIBundle\Logger\MonitorableLogger;
use Darkilliant\ProcessUIBundle\ProcessNotifier\WebProcessNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function homeAction()
    {
        return $this->render('@DarkilliantProcessUI/process/list.html.twig', [
            'processCollection' => $this->getParameter('darkilliant_process')['process'] ?? [],
        ]);
    }

    public function runnerAction($processName)
    {
        return $this->render('@DarkilliantProcessUI/process/run.html.twig', [
            'processName' => $processName
        ]);
    }

    public function runAction($processName)
    {
        $stepRunner = $this->get(StepRunner::class);
        $logger = $this->get(MonitorableLogger::class);
        $notifier = $this->get(WebProcessNotifier::class);

        $stepRunner->setNotifier($notifier);
        $process = $stepRunner->buildConfigurationProcess($processName, MonitorableLogger::class);

        $logger->setHandler(function($message) {
            echo 'data: '.json_encode([
                'type' => 'log',
                'message' => $message['message'],
                'context' => $message['context'],
                'level' => $message['level'],
            ]);
            echo PHP_EOL.PHP_EOL;
            ob_flush();
            flush();
        });

        $notifier->setHandler(function($count, $progress) {
            echo 'data: '.json_encode([
                    'type' => 'progress',
                    'count' => $count,
                    'progress' => $progress,
                ]);
            echo PHP_EOL.PHP_EOL;
            ob_flush();
            flush();
        });

        $response = new StreamedResponse(function() use ($stepRunner, $process) {
            $stepRunner->run(
                $process,
                []
            );
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }
}