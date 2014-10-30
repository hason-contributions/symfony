<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug;

use Symfony\Component\Debug\Exception\FlattenException;

/**
 * ExceptionFlattener converts an Exception to FlattenException
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class ExceptionFlattener
{
    /**
     * @var FlattenExceptionProcessorInterface[]
     */
    private $processors = array();

    /**
     * Constructor.
     *
     * @param array $processors The collection of processors
     */
    public function __construct($processors = array())
    {
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    /**
     * Adds an exception processor
     *
     * @param FlattenExceptionProcessorInterface $processor
     */
    public function addProcessor(FlattenExceptionProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * Flattens an exception
     *
     * @param \Exception $exception The raw exception
     *
     * @return FlattenException
     */
    public function flatten(\Exception $exception)
    {
        $e = FlattenException::create($exception);
        foreach ($this->processors as $processor) {
            $e = $processor->process($exception, $e);
        }

        return $e;
    }
}
