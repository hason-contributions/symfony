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

class TimeNode extends ArgumentNode
{
    protected $type = 'time';

    /**
     * {@inheritdoc}
     */
    protected function doFormat($argument)
    {
        $formatter = new \IntlDateFormatter($this->getLocale(), null, $this->getType());

        return $formatter->format($argument);
    }
}