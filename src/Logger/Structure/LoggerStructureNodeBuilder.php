<?php
namespace Athena\Logger\Structure;

class LoggerStructureNodeBuilder
{
    /**
     * @var LoggerStructureNode
     */
    private $parent;

    /**
     * @param \Athena\Logger\Structure\LoggerStructureNode $parent
     *
     * @return \Athena\Logger\Structure\LoggerStructureNodeBuilder
     */
    public function setParent(LoggerStructureNode $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return LoggerStructureNode
     */
    public function newNode()
    {
        $node = new LoggerStructureNode();

        if ($this->parent !== null) {
            $this->parent->addChild($node);
        }

        return $node;
    }
}

