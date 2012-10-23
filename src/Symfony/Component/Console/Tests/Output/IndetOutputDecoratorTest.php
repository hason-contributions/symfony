<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Output;

use Symfony\Component\Console\Output\IndentOutputDecorator;
use Symfony\Component\Console\Output\StreamOutput;

class IndentOutputDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIndent()
    {
        $output = $this->createOutput('   ');
        $output->writeln('bar');
        $output->write('foo ');
        $output->writeln('line');

        $this->assertEquals('bar'.PHP_EOL.'   foo line'.PHP_EOL.'   ', $this->getStream($output));
    }

    protected function createOutput($indent = '  ')
    {
        return new IndentOutputDecorator(new StreamOutput(fopen('php://memory', 'r+', false)), $indent);
    }

    protected function getStream($output)
    {
        rewind($output->getStream());

        return stream_get_contents($output->getStream());
    }
}
