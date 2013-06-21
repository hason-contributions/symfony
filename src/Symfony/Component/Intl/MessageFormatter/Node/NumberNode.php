<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\MessageFormatter\Node;

/**
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class NumberNode extends ArgumentNode
{
    protected $type = 'number';

    /**
     * {@inheritdoc}
     */
    protected function doFormat($argument)
    {
        $formatter = new \NumberFormatter($this->getLocale(), $this->getStyle());

        return $formatter->format($argument);
    }

    /**
     * {@inheritdoc}
     */
    protected function doParse()
    {
        $formatter = new \NumberFormatter($this->getLocale(), $this->getStyle());

        return $formatter->parse($argument);
    }

    protected function getName()
    {
        return 'number';
    }
}