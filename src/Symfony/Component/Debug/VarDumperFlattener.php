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
use Symfony\Component\Debug\ExceptionFlattenerInterface;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;

/**
 * Class ExceptionProcessor
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class VarDumperFlattener implements ExceptionFlattenerInterface
{
    private $cloner;

    public function __construct(ClonerInterface $cloner)
    {
        $this->cloner = $cloner;
    }

    public function flatten(\Exception $exception, FlattenException $flattenException, $options = array())
    {
        $trace = $flattenException->getTrace();
        $origTrace = $exception->getTrace();

        switch (count($trace) - count($origTrace)) {
            case 0:
                $from = 0;
                break;
            case 1:
                $from = 1;
                break;
            default:
                throw new \InvalidArgumentException();
        }

        foreach ($origTrace as $key => $entry) {
            $parameters = array();
            if (isset($entry['function'])) {
                if (isset($entry['class'])) {
                    $ref = new \ReflectionMethod($entry['class'], $entry['function']);
                } else {
                    $ref = new \ReflectionFunction($entry['function']);
                }

                $parameters = $ref->getParameters();
            }

            foreach ($entry['args'] as $position => $value) {
                if (isset($parameters[$position])) {
                    $trace[$from + $key]['cloned_args'][$parameters[$position]->getName()] = $this->cloner->cloneVar($value);
                } else {
                    $trace[$from + $key]['cloned_args'][] = $this->cloner->cloneVar($value);
                }
            }
        }

        $flattenException->setRawTrace($trace);

        return $flattenException;
    }
}
