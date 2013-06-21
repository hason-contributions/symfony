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
use Symfony\Component\Intl\MessageFormatter\PatternParser;
use Symfony\Component\Intl\MessageFormatter\Node\MessageNode;

/**
 * Note that there are some values written like -2147483647 - 1. This is the lower 32bit int max and is a known
 * behavior of PHP.
 */
class PatternParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvide getApostropheMode
     */
    public function testApostropheMode($expected, $pattern, $apostropheMode = PatternParser::APOSTROPHE_DOUBLE_OPTIONAL)
    {
        $parser = new PatternParser();

        $this->assertEquals($expected, $parser->parse($pattern));
    }

    public function getApostropheMode()
    {

        return array(
            array(new MessageNode('I see {many}'), "I see '{many}'"),
            array(new MessageNode('I see {many}'), "I see '{many}'", PatternParser::APOSTROPHE_DOUBLE_REQUIRED),
            array(new MessageNode("I said {'Wow!'}"), "I said '{''Wow!''}'", PatternParser::APOSTROPHE_DOUBLE_REQUIRED),
            array(new MessageNode("I said {'Wow!'}"), "I said '{''Wow!''}'", PatternParser::APOSTROPHE_DOUBLE_REQUIRED),
        )
    }
}