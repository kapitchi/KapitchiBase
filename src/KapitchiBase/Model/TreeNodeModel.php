<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\Model;

use RuntimeException as UnknownDataException;

class TreeNodeModel {
    private $parent;
    private $children;
    private $depth;
    
    private $parentSet = false;
    private $childrenSet = false;
    
    protected $item;

    public function isRoot()
    {
        return ($this->getParent() === null);
    }
    
    public function isLeaf()
    {
        return (count($this->getChildren()) == 0);
    }
    
    public function getParent()
    {
        if(!$this->parentSet) {
            throw new UnknownDataException("Parent hasn't been set");
        }
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
        
        $this->parentSet = true;
    }

    public function getChildren()
    {
        if (!$this->childrenSet) {
            throw new UnknownDataException("Children haven't been set");
        }
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
        
        $this->childrenSet = true;
    }
    
    public function getDepth()
    {
        if($this->depth !== null) {
            return $this->depth;
        }
        
        //if level is not explicitely set try to find out
        $node = $this;
        $depth = 0;
        while (!$node->isRoot()) {
            $node = $node->getParent();
            $depth++;
        }
        $this->depth = $depth;
        return $this->depth;
    }
    
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }
    
    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }
}