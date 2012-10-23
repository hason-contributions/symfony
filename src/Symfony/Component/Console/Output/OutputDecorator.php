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

use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 *
 * @author Martin Hasoň <hason@itstudio.cz>
 */
abstract class OutputDecorator implements OutputDecoratorInterface
{
    /**
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * Constructor.
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = 0)
    {
        return $this->output->write($messages, $newline, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $type = 0)
    {
        return $this->output->writeln($messages, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setVerbosity($level)
    {
        return $this->output->setVerbosity($level);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function setDecorated($decorated)
    {
        return $this->output->setDecorated($decorated);
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        return $this->output->setFormatter($formatter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        return $this->output->getFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function getDecoratedOutput()
    {
        return $this->output;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->output, $method), $args);
    }

    /**
     * Gets a message.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param Boolean      $newline  Whether to add a newline or not
     * @param integer      $type     The type of output
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @see Output::write()
     *
     * @return string
     */
    protected function getOutputMessage($messages, $newline = false, $type = 0)
    {
        if (self::VERBOSITY_QUIET === $this->getVerbosity()) {
            return '';
        }

        $output = '';
        foreach ((array) $messages as $message) {
            switch ($type) {
                case self::OUTPUT_NORMAL:
                    $message = $this->getFormatter()->format($message);
                    break;
                case self::OUTPUT_RAW:
                    break;
                case self::OUTPUT_PLAIN:
                    $message = strip_tags($this->getFormatter()->format($message));
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown output type given (%s)', $type));
            }

            $output .= $message;
            if ($newline) {
                $output .= PHP_EOL;
            }
        }

        return $output;
    }
}
