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
class IndentOutputDecorator extends OutputDecorator
{
    private $indent;

    /**
     * {@inheritdoc}
     */
    public function __construct(OutputInterface $output, $indent = ' ')
    {
        $this->output = $output;
        $this->setIndent($indent);
    }

    /**
     * Gets indent chars
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Sets indent chars
     *
     * @param string $indent Indent chars
     */
    public function setIndent($indent)
    {
        $this->indent = $indent;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = 0)
    {
        $messages = $this->indent($messages, $newline, $type);

        return $this->output->write($messages, false, OutputInterface::OUTPUT_RAW);
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = 0)
    {
        $messages = $this->indent($messages, true, $type);

        return $this->output->write($messages, false, OutputInterface::OUTPUT_RAW);
    }

    private function indent($messages, $newline, $type)
    {
        $messages = $this->getOutputMessage($messages, $newline, $type);
        if ($indent = $this->indent) {
            $messages = preg_replace_callback('#(\r?\n)(.*?)#', function ($m) use ($indent) { return $m[1].$indent.$m[2]; }, $messages);
        }

        return $messages;
    }
}
