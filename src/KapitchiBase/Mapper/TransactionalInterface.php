<?php

namespace KapitchiBase\Mapper;

interface TransactionalInterface {
    public function beginTransaction();
    public function commit();
    public function rollback();
}