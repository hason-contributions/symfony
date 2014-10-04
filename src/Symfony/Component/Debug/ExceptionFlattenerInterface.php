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
 * Class ExceptionProcessor
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
interface ExceptionFlattenerInterface
{
    /**
     * Flattens exception
     *
     * @param \Exception       $exception
     * @param FlattenException $flattenException
     * @param array            $options
     *
     * @return FlattenException
     */
    public function flatten(\Exception $exception, FlattenException $flattenException, $options = array());
}
