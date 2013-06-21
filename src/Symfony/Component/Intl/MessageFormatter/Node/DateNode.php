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

class DateNode extends ArgumentNode
{
    /**
     * {@inheritdoc}
     */
    protected function doFormat($argument)
    {
        $formatter = new \IntlDateFormatter($this->getLocale(), $this->getType());

        return $formatter->format($argument);
    }

    /**
     * {@inheritdoc}
     */
    protected function doParse()
    {
        $formatter = new \IntlDateFormatter($this->getLocale(), $this->getType());

        return $formatter->parse($argument);
    }
}