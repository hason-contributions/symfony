<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

/**
 * @author Martin Hasoň <hason@itstudio.cz>
 */
class BufferOutputDecorator extends OutputDecorator
{
    private $buffer;
    private $forward;

    public function __construct(OutputInterface $output, $forward = true)
    {
        parent::__construct($output);
        $this->buffer = array();
        $this->forward = true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = 0)
    {
        $this->buffer[] = array($messages, $newline, $type);
        if ($this->forward) {
            $this->output->write($messages, $newline, $type);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = 0)
    {
        $this->write($messages, true, $type);
    }

    public function forwardBuffer()
    {
        foreach ($this->buffer as $write) {
            $this->output->write($write[0], $write[1], $write[2]);
        }
    }

    public function getBuffer()
    {
        if (self::VERBOSITY_QUIET === $this->getVerbosity()) {
            return;
        }

        $output = '';
        foreach ($this->buffer as $buffer) {
            $output .= $this->getOutputMessage($buffer[0], $buffer[1], $buffer[2]);
        }

        return $output;
    }

    /**
     * Clears buffer
     */
    public function clearBuffer()
    {
        $this->buffer = array();
    }

    public function setForward($forward)
    {
        $this->forward = (Boolean) $forward;
    }
}
