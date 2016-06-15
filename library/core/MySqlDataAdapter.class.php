<?php

class MySqlDataAdapter {

    protected $_server, $_username, $_password, $_errorInfo;
    public $dbName;
    public $connection;
    protected $_result;

    const DATETIME = 'Y-m-d H:i:s';
    const DATE     = 'Y-m-d';

    public function __construct($server, $username, $password, $dbName, $connect_now = true, $persistent = false, $pdoFlags = false) {
        $this->_server   = $server;
        $this->_username = $username;
        $this->_password = $password;
        $this->dbName    = $dbName;

        if ($connect_now) {
            $this->connect($persistent, $pdoFlags);
        }
    }

    public function __destruct() {
        $this->close();
    }

    public function connect($persistent = false, $pdoFlags = false) {

        if ($persistent === true) {
            $pdoFlags = ($pdoFlags !== false) ? array_merge($pdoFlags, PDO::ATTR_PERSISTENT) : PDO::ATTR_PERSISTENT;
        }

        $flags = $this->_verifyPdoFlags($pdoFlags);


        $dsn = "mysql:host={$this->_server}";
        try {

            $this->connection = new Pdo($dsn, $this->_username, $this->_password, $flags);
        } catch (PDOException $e) {
            $this->_handleError($e, true);
            return false;
        }

        $this->selectDb($this->dbName);

        return $this->connection;
    }

    public function selectDb($dbName, $oneOff = false) {
        if ($this->connection) {
            if ($oneOff === false) {
                $this->dbName = $dbName;
            }

            try {
                return $this->query("USE `{$dbName}`");
            } catch (PDOException $e) {
                $this->_handleError($e);
            }
        }
        return false;
    }

    public function query($queryStr, $unbuffered = false) {

        $result = false;
        try {
            $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, !$unbuffered);

            $result        = $this->connection->query($queryStr);
            $this->_result = $result;
        } catch (PDOException $e) {
            $this->_handleError($e, true, "Query String: " . $queryStr);
        }
        return $result;
    }

    public function update(array $values, $table, $where = false, $limit = false) {
        if (count($values) < 0)
            return false;

        $fields   = array();
        foreach ($values as $field => $val)
            $fields[] = "`" . $field . "` = '" . $this->escapeString($val) . "'";

        $where = ($where) ? " WHERE " . $where : '';
        $limit = ($limit) ? " LIMIT " . $limit : '';

        if ($this->query("UPDATE " . $table . " SET " . implode($fields, ", ") . $where . $limit))
            return true;
        else
            return $this->_lastError();
    }

    public function insert(array $values, $table) {
        if (count($values) < 0)
            return false;

        foreach ($values as $field => $val)
            $values[$field] = $this->escapeString($val);

        if ($this->query("INSERT INTO " . $table . " (`" . implode(array_keys($values), "`, `") . "`) VALUES ('" . implode($values, "', '") . "')"))
            return true;
        else
            return $this->_lastError();
    }

    public function select($fields, $table, $where = false, $orderby = false, $limit = false, $join = '', $groupby = '') {
        if (is_array($fields))
            $fields = "`" . implode($fields, "`, `") . "`";

        $orderby = ($orderby) ? " ORDER BY " . $orderby : '';
        $where   = ($where) ? " WHERE " . $where : '';
        $limit   = ($limit) ? " LIMIT " . $limit : '';
        
        $sql_str = "SELECT " . $fields . " FROM " . $table . " " . $join . " " . $where . " " . $groupby . $orderby . $limit;

        $res = $this->query($sql_str);

        if ($this->numRows($res) > 0) {
            $rows = array();

            while ($r      = $this->fetchAssoc($res))
                $rows[] = $r;

            return $rows;
        } else
            return false;
    }

    public function selectOne($fields, $table, $where = false, $orderby = false, $join = '', $groupby = '') {
        $result = $this->select($fields, $table, $where, $orderby, '1', $join, $groupby);

        return $result[0];
    }

    public function selectOneValue($field, $table, $where = false, $orderby = false, $join = '', $groupby = '', $dbname = false) {
        if ($dbname) {
            $this->selectDb($dbname, true);
        }

        $result = $this->selectOne($field, $table, $where, $orderby, $join, $groupby);

        $this->selectDb($dbname, false);

        return $result[$field];
    }

    public function delete($table, $where = false, $limit = 1) {
        $where = ($where) ? "WHERE {$where}" : "";
        $limit = ($limit) ? "LIMIT {$limit}" : "";

        if ($this->query("DELETE FROM `{$table}` {$where} {$limit}"))
            return true;
        else
            return $this->_lastError();
    }

    public function fetchArray($result = false, $resultType = 3) {
        $this->_verifyResult($result);
        switch ($resultType) {
            case 1:

                return $result->fetchAll(PDO::FETCH_ASSOC);
            case 2:

                return $result->fetchAll(PDO::FETCH_NUM);
            case 3:

                return $result->fetchAll();
            case 4:

                return $result->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public function fetchAssoc($result = false) {
        $this->_verifyResult($result);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchRow($result = false) {
        $this->_verifyResult($result);
        return $result->fetchAll(PDO::FETCH_NUM);
    }

    public function fetchObject($result = false) {
        $this->_verifyResult($result);
        return $result->fetchAll(PDO::FETCH_OBJ);
    }

    public function fetchOne($result = false) {
        $this->_verifyResult($result);
        list($ret) = $this->fetchRow($result);
        return $ret;
    }

    public function escapeString($string) {
        try {
            $string = $this->connection->quote($string);
            return substr($string, 1, -1);
        } catch (PDOException $e) {
            $this->_loadError($link, $e);
        }

        return false;
    }

    public function numRows($result) {
        $this->_verifyResult($result);
        if (is_array($result)) {
            return count($result);
        }

        $query  = $result->queryString;
        $cloned = $this->query($query);
        if ($cloned) {
            $data = $cloned->fetchAll();
        }
        return count($data);
    }

    public function numFields($result) {
        $this->_verifyResult($result);
        if (is_array($result)) {
            return count($result);
        }

        $data = $result->fetch(PDO::FETCH_NUM);
        return count($data);
    }

    public function insertId() {
        return (int) $this->connection->lastInsertId();
    }

    public function affectedRows() {
        $result = $this->_verifyResult(false);
        return $result->rowCount();
    }

    public function freeResult(&$result) {
        if (is_array($result)) {
            $result = false;
            return true;
        }

        if (get_class($result) != 'PDOStatement') {
            return false;
        }

        return $result->closeCursor();
    }

    public function close() {
        if (isset($this->connection)) {
            $this->connection = null;
            unset($this->connection);
            return true;
        }
        return false;
    }

    protected function _mapPdoType($type) {

        $type = strtolower($type);
        switch ($type) {
            case 'tiny':
            case 'short':
            case 'long':
            case 'longlong';
            case 'int24':
                return 'int';
            case 'null':
                return null;
            case 'varchar':
            case 'var_string':
            case 'string':
                return 'string';
            case 'blob':
            case 'tiny_blob':
            case 'long_blob':
                return 'blob';
            default:
                return $type;
        }
    }

    protected function _verifyResult(&$result) {
        if ($result == false) {
            $result = $this->_result;
        } else {
            if (gettype($result) !== 'resource' && is_string($result)) {
                $result = $this->query($result);
            }
        }
    }

    protected function _verifyPdoFlags($flags) {
        if ($flags == false || empty($flags)) {
            return array();
        }

        if (!is_array($flags)) {
            $flags = array($flags);
        }

        $pdoParams = array();
        foreach ($flags as $flag) {
            switch ($flag) {

                case 2:
                    $params = array(PDO::MYSQL_ATTR_FOUND_ROWS => true);
                    break;

                case 32:
                    $params = array(PDO::MYSQL_ATTR_COMPRESS => true);
                    break;

                case 128:
                    $params = array(PDO::MYSQL_ATTR_LOCAL_INFILE => true);
                    break;

                case 256:
                    $params = array(PDO::MYSQL_ATTR_IGNORE_SPACE => true);
                    break;

                case 12:
                    $params = array(PDO::ATTR_PERSISTENT => true);
                    break;
            }

            $pdoParams[] = $params;
        }

        return $pdoParams;
    }

    protected function _handleError($e, $throw = true, $extraInfo = false) {
        // Reset errors
        if ($e === false || is_null($e)) {
            $this->_errorInfo = array('error' => "", 'errno' => 0);
            return;
        }
        // Set error
        $this->_errorInfo = array('error' => $e->getMessage(), 'errno' => $e->getCode());

        if ($throw) {
            $s = "<br />Error Code:" . $this->_errorInfo['errno'] . "<br /> Description: " . $this->_errorInfo['error'] . "<br />";
            if (!empty($extraInfo)) {
                $s .= $extraInfo . "<br />";
            }
            trigger_error($s, E_USER_ERROR);
        }
    }

    protected function _lastError() {
        $error = '';

        if ($this->connection) {
            $error = $this->connection->errorInfo()[2];
        }

        return $error;
    }

    protected function _lastErrNo() {
        $error = '';

        if ($this->connection) {
            $error = $this->connection->errorCode()[0];
        }

        return $error;
    }

}
