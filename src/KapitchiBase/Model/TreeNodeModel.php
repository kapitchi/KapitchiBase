<?php

namespace KapitchiBase\Model;

use ZfcBase\Model\ModelAbstract;

class TreeNodeModel extends ModelAbstract {
    protected $parent;
    protected $children;
    
    public function isRoot() {
        return ($this->getParent() === null);
    }
    
    public function isLeaf() {
        return (count($this->getChildren()) == 0);
    }
    
    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function getChildren() {
        return $this->children;
    }

    public function setChildren($children) {
        $this->children = $children;
    }
    
    public function getLevel() {
        $node = $this;
        $level = 0;
        while(!$node->isRoot()) {
            $node = $node->getParent();
        }
        
        return $level;
    }

}