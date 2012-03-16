<?php

namespace KapitchiBase\Rest;

interface RestfulService {
    public function getList(array $filter);
    public function get($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}