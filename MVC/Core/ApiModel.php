<?php

class ApiModel extends connectDB {
    protected function escape($value) {
        return mysqli_real_escape_string($this->con, (string) $value);
    }

    protected function fetchAll($sql) {
        return $this->select($sql);
    }

    protected function fetchOne($sql) {
        return $this->selectOne($sql);
    }

    protected function run($sql) {
        return $this->execute($sql);
    }

    protected function affectedRows() {
        return mysqli_affected_rows($this->con);
    }

    protected function createId($prefix) {
        return strtoupper($prefix) . date('YmdHis') . random_int(100, 999);
    }
}
