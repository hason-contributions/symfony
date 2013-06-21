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

abstract class AbstractNode implements \Countable, \IteratorAggregate
{
    protected $attributes;

    protected $nodes;

    /**
     * Returns true if the attribute is defined.
     *
     * @param  string  The attribute name
     *
     * @return Boolean true if the attribute is defined, false otherwise
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Gets an attribute.
     *
     * @param  string The attribute name
     *
     * @return mixed The attribute value
     */
    public function getAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new LogicException(sprintf('Attribute "%s" does not exist for Node "%s".', $name, get_class($this)));
        }

        return $this->attributes[$name];
    }

    /**
     * Sets an attribute.
     *
     * @param string The attribute name
     * @param mixed  The attribute value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Removes an attribute.
     *
     * @param string The attribute name
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Returns true if the node with the given identifier exists.
     *
     * @param  string  The node name
     *
     * @return Boolean true if the node with the given name exists, false otherwise
     */
    public function hasNode($name)
    {
        return array_key_exists($name, $this->nodes);
    }

    /**
     * Gets a node by name.
     *
     * @param  string The node name
     *
     * @return Twig_Node A Twig_Node instance
     */
    public function getNode($name)
    {
        if (!array_key_exists($name, $this->nodes)) {
            throw new LogicException(sprintf('Node "%s" does not exist for Node "%s".', $name, get_class($this)));
        }

        return $this->nodes[$name];
    }

    /**
     * Sets a node.
     *
     * @param string    The node name
     * @param Twig_Node A Twig_Node instance
     */
    public function setNode($name, $node = null)
    {
        $this->nodes[$name] = $node;
    }

    /**
     * Removes a node by name.
     *
     * @param string The node name
     */
    public function removeNode($name)
    {
        unset($this->nodes[$name]);
    }

    public function count()
    {
        return count($this->nodes);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }

    public function __toString()
    {
        return '';
    }

    abstract public function format(array $arguments);

    abstract public function parse();
}