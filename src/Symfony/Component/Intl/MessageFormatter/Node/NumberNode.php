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

class NumberNode extends ArgumentNode
{
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
}