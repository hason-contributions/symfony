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
interface OutputDecoratorInterface extends OutputInterface
{
    /**
     * Returns decorated output
     *
     * @return OutputInterface
     */
    public function getDecoratedOutput();
}
