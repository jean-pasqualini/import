<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\State;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Darkilliant\ProcessBundle\Runner\StepRunner;

class ProcessState extends AbstractLogger
{
    const RESULT_KO = 1;
    const RESULT_SKIP = 2;
    const RESULT_OK = 3;
    const RESULT_BREAK = 4;
    const RESULT_EXIT = 5;

    private $data;
    private $loopContext;
    private $logContext = [];
    private $context = [];
    private $options = [];
    private $logger;
    private $result;

    private $dryRun = false;

    /** @var StepRunner */
    private $stepRunner;

    /** @var \Traversable */
    private $iterator;

    private $name;

    private $loop;

    public function __construct(array $context, LoggerInterface $logger, StepRunner $stepRunner)
    {
        $this->context = $context;
        $this->logger = $logger;
        $this->stepRunner = $stepRunner;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getContext($key, $default = null)
    {
        return $this->context[$key] ?? $default;
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function noLoop()
    {
        $this->loop = null;
        $this->loopContext = null;
    }

    public function loop(int $index, int $count, bool $last)
    {
        $this->loop = [
            'index' => $index,
            'count' => $count,
            'last' => $last,
        ];
    }

    public function isLoop()
    {
        return (bool) $this->loop;
    }

    public function setContext(string $key, $value, bool $logContext = true)
    {
        $this->context[$key] = $value;
        if ($logContext) {
            $this->logContext[$key] = $value;
        }
    }

    public function hasContext(string $key): bool
    {
        return isset($this->context[$key]);
    }

    public function setLoopContext($value)
    {
        $this->loopContext = $value;
    }

    public function getLoopContext()
    {
        return $this->loopContext;
    }

    public function getRawContext(): array
    {
        return $this->context;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption($name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): ProcessState
    {
        $this->options = $options;

        return $this;
    }

    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, array_merge($context, $this->logContext));
    }

    public function getResult()
    {
        return $this->result;
    }

    public function markFail()
    {
        $this->result = self::RESULT_KO;
    }

    public function markIgnore()
    {
        $this->result = self::RESULT_SKIP;
    }

    public function markSuccess()
    {
        $this->result = self::RESULT_OK;
    }

    public function markBreak()
    {
        $this->result = self::RESULT_BREAK;
    }

    public function markExit()
    {
        $this->result = self::RESULT_EXIT;
    }

    public function getStepRunner(): StepRunner
    {
        return $this->stepRunner;
    }

    public function duplicate($logger = null): self
    {
        $duplicate = new self($this->context, $logger ?? $this->logger, $this->stepRunner);
        $duplicate->setData($this->data);

        return $duplicate;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @param \Traversable $iterator
     */
    public function setIterator($iterator)
    {
        $this->iterator = $iterator;
    }

    public function setDryRun(bool $dryRun)
    {
        $this->dryRun = $dryRun;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
