<?php
namespace Athena\Logger\Structure;

class LoggerStructureNode
{
    /**
     * @var \Athena\Logger\Structure\LoggerStructureNodeBuilder
     */
    private $parent;

    /**
     * @var \Athena\Logger\Structure\LoggerStructureNode[]
     */
    private $children = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param \Athena\Logger\Structure\LoggerStructureNode $node
     *
     * @return \Athena\Logger\Structure\LoggerStructureNode
     */
    public function setParent(LoggerStructureNode $node = null)
    {
        $this->parent = $node;
        return $this;
    }

    /**
     * @param \Athena\Logger\Structure\LoggerStructureNode $node
     *
     * @return \Athena\Logger\Structure\LoggerStructureNode
     */
    public function addChild(LoggerStructureNode $node)
    {
        $this->children[] = $node->setParent($this);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return \Athena\Logger\Structure\LoggerStructureNode
     */
    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return \Athena\Logger\Structure\LoggerStructureNode[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return \Athena\Logger\Structure\LoggerStructureNodeBuilder
     */
    public function withChildren()
    {
        return (new LoggerStructureNodeBuilder())->setParent($this);
    }

    /**
     * @return \Athena\Logger\Structure\LoggerStructureNode
     */
    public function end()
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $nodeArray = $this->getAttributes();

        if ($this->hasChildren()) {
            $nodeArray['children'] = [];

            foreach ($this->getChildren() as $childNode) {
                $nodeArray['children'][] = $childNode->toArray();
            }
        }

        return $nodeArray;
    }
}

