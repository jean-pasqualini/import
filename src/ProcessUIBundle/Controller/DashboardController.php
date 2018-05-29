<?php

namespace Darkilliant\ProcessUIBundle\Controller;

use Darkilliant\ProcessBundle\Configuration\ConfigurationStep;
use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessUIBundle\Logger\MonitorableLogger;
use Darkilliant\ProcessUIBundle\ProcessNotifier\WebProcessNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function homeAction()
    {
        return $this->render('@DarkilliantProcessUI/process/list.html.twig', [
            'processCollection' => $this->getParameter('darkilliant_process')['process'] ?? [],
        ]);
    }

    public function runnerAction(Request $request, $processName)
    {
        $stepRunner = $this->get(StepRunner::class);
        $process = $stepRunner->buildConfigurationProcess($processName);
        /** @var ConfigurationStep $firstStep */
        $firstStep = $process->getSteps()[0];
        $options = $firstStep->getOptions();

        $context = [];
        $configurable = [];
        $runnable = true;

        foreach ($options as $name => $value) {
            if (is_scalar($value) && strpos($value, '@') !== false) {
                $configurable[] = $name;
            }
        }
        if (!empty($configurable)) {
            $runnable = false;

            if ($request->isMethod('POST')) {
                $context = $request->request->all();
                $runnable = true;
            }
        }

        return $this->render('@DarkilliantProcessUI/process/run.html.twig', [
            'processName' => $processName,
            'configurable' => $configurable,
            'runnable' => $runnable,
            'context' => base64_encode(json_encode($context)),
        ]);
    }

    public function runAction(Request $request, $processName)
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

        $context = json_decode(base64_decode($request->query->get('context')), true);

        $response = new StreamedResponse(function() use ($stepRunner, $process, $context) {
            $stepRunner->run(
                $process,
                $context
            );
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }
}