<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Tests\MessageFormatter;

use Symfony\Component\Intl\Globals\IntlGlobals;
use Symfony\Component\Intl\NumberFormatter\NumberFormatter;
use Symfony\Component\Intl\Util\IntlTestHelper;

/**
 * Note that there are some values written like -2147483647 - 1. This is the lower 32bit int max and is a known
 * behavior of PHP.
 */
class MessagePatternTest extends \PHPUnit_Framework_TestCase
{

}