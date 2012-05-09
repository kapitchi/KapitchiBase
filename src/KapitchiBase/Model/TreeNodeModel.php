<?php

namespace KapitchiBase\Model;

use ZfcBase\Model\ModelAbstract,
    RuntimeException as UnknownDataException;

class TreeNodeModel extends ModelAbstract {
    private $parent;
    private $children;
    private $depth;
    
    private $parentSet = false;
    private $childrenSet = false;
    
    public function isRoot() {
        return ($this->getParent() === null);
    }
    
    public function isLeaf() {
        return (count($this->getChildren()) == 0);
    }
    
    public function getParent() {
        if(!$this->parentSet) {
            throw new UnknownDataException("Parent hasn't been loaded");
        }
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;
        
        $this->parentSet = true;
    }

    public function getChildren() {
        if (!$this->parentSet) {
            throw new UnknownDataException("Children haven't been loaded");
        }
        return $this->children;
    }

    public function setChildren($children) {
        $this->children = $children;
        
        $this->childrenSet = true;
    }
    
    public function getDepth() {
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
    
    public function setDepth($depth) {
        $this->depth = $depth;
    }
    
}