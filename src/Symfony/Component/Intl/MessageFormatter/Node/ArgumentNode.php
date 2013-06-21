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

/**
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class ArgumentNode extends AbstractNode
{
    protected $name;
    protected $type;
    protected $locale;

    public function __construct($name)
    {
        $this->setName($name);
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

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

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

    /**
     * {@inheritdoc}
     */
    public function getPattern()
    {
        $pattern = '{'.$this->getName();
        if (null !== $this->type) {
            $pattern .= ', '.$this->type;
        }

        return $pattern.'}';
    }
}