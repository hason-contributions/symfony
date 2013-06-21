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

use Symfony\Component\Intl\Globals\IntlGlobals;

class ArgumentNode extends AbstractNode
{
    protected $name;
    protected $type;

    public function __construct($name)
    {
        parent::__construct();
    }

    public function getName()
    {
        return $this->name = name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $arguments)
    {
        if (!array_key_exists($this->getName(), $arguments)) {
            throw new Exception(IntlGlobals::U_ILLEGAL_ARGUMENT_ERROR);
        }
    }
}