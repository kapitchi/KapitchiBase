<?php

namespace KapitchiBase\Mapper;

interface Transactional {
    public function beginTransaction();
    public function commit();
}