<?php

/* -------------------------------------------------------+
  | PHP-Fusion Content Management System
  | Copyright (C) PHP-Fusion Inc
  | https://www.php-fusion.co.uk/
  +--------------------------------------------------------+
  | Filename: pdo_database_driver.php
  | Author: Yodix
  | Co-Author: Joakim Falk (Domi)
  | Modified: Dennis Vorpahl and Philipp Horna
  +--------------------------------------------------------+
  | This program is released as free software under the
  | Affero GPL license. You can redistribute it and/or
  | modify it under the terms of this license which you
  | can read by viewing the included agpl.txt or online
  | at www.gnu.org/licenses/agpl.html. Removal of this
  | copyright header is strictly prohibited without
  | written permission from the original author(s).
  +-------------------------------------------------------- */
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

/**
 * Opens or re-uses a connection to a MySQL server.
 * @param string $db_host The MySQL server. It can also include a port number. e.g. "hostname:port" or a path to a local socket e.g. ":/path/to/socket" for the localhost.
 *      If the PHP directive mysql.default_host is undefined (default), then the default value is 'localhost:3306'. 
 *      In SQL safe mode, this parameter is ignored and value 'localhost:3306' is always used.
 * @param string $db_user The username. Default value is defined by mysql.default_user. 
 *      In SQL safe mode, this parameter is ignored and the name of the user that owns the server process is used.
 * @param string $db_pass The password. Default value is defined by mysql.default_password. 
 *      In SQL safe mode, this parameter is ignored and empty password is used.
 * @param string $db_name The name of the database that is to be selected.
 * @return mixed Returns a PDO object on success or an Excpetion on failure
 */
function dbconnect($db_host, $db_user, $db_pass, $db_name) {
    global $pdo;
    try {
        $pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
    } catch (PDOException $error) {
        die("<strong>Unable to select MySQL database</strong><br />" . $error->getMessage());
    }
}

/**
 * dbquery() sends an unique query to the database.
 * @param resource $query A SQL query, The query string should not end with a semicolon.
 * @param array $execute
 * @return mixed FALSE or an array $result
 */
function dbquery($query, $execute = array()) {
    global $pdo, $mysql_queries_count, $mysql_queries_time;
    $mysql_queries_count++;
    $query_time = get_microtime();
    $result = $pdo->prepare($query);
    $query_time = substr((get_microtime() - $query_time), 0, 7);
    $mysql_queries_time[$mysql_queries_count] = array($query_time, $query);
    if (!$result) {
        print_r($pdo->errorInfo());
        return FALSE;
    } else {
        $result->execute($execute);
        return $result;
    }
}

/**
 * @param resource $query A SQL query, The query string should not end with a semicolon.
 * @return array $result 
 */
function dbquery_exec($query) {
    global $pdo, $mysql_queries_count, $mysql_queries_time;
    $mysql_queries_count++;
    $query_time = get_microtime();
    $result = $pdo->exec($query);
    $query_time = substr((get_microtime() - $query_time), 0, 7);
    $mysql_queries_time[$mysql_queries_count] = array($query_time, $query);
    return $result;
}

/**
 * Counts records by $field (can be all fields if this value is (*) ) from $table in some $conditions (default no conditions) written in SQL syntax.
 * @param string $field The fields you want to count in the database
 * @param string $table The table you want to count from
 * @param string $conditions The conditions you want to the results to match
 * @param array $execute
 * @return mixed This function will return the number of rows in the $table matching the $conditions if any.
 *      This function will return false if there are no rows or on failure.
 */
function dbcount($field, $table, $conditions = "", $execute = array()) {
    global $pdo, $mysql_queries_count, $mysql_queries_time;
    $mysql_queries_count++;
    $cond = ($conditions ? " WHERE " . $conditions : "");
    $query_time = get_microtime();
    $result = $pdo->prepare("SELECT COUNT" . $field . " FROM " . $table . $cond);
    $query_time = substr((get_microtime() - $query_time), 0, 7);
    $mysql_queries_time[$mysql_queries_count] = array($query_time, "SELECT COUNT" . $field . " FROM " . $table . $cond);
    if (!$result) {
        print_r($result->errorInfo());
        return FALSE;
    } else {
        $result->execute($execute);
        return $result->fetchColumn();
    }
}

/**
 * Retrieves the contents of one cell from a MySQL result set.
 * @param resource $query The query resource that is being evaluated. This result comes from a call to dbquery().
 * @param string $row The row number from the result that's being retrieved. Row numbers start at 0.
 * @return array $result The contents of one cell from a MySQL result set on success, or FALSE on failure.
 */
function dbresult($query, $row) {
    global $pdo, $mysql_queries_count, $mysql_queries_time;
    $query_time = get_microtime();
    $data = $query->fetchAll();
    $query_time = substr((get_microtime() - $query_time), 0, 7);
    $mysql_queries_time[$mysql_queries_count] = array($query_time, $query);
    if (!$query) {
        print_r($query->errorInfo());
        return FALSE;
    } else {
        $result = $query->getColumnMeta(0);
        return $data[$row][$result['name']];
    }
}

/**
 * This function will give you the total number of rows in a given database query. 
 * This is often used to check for rows in a result before displaying content.
 * @param resource $query
 * @return mixed The number of rows as int, or FALSE on failure.
 */
function dbrows($query) {
    return $query->rowCount();
}

/**
 * Returns an associative array that corresponds to the fetched row and moves the internal data pointer ahead. * 
 * @param resource $query
 * @return array Returns an associative array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
 */
function dbarray($query) {
    global $pdo;
    $query->setFetchMode(PDO::FETCH_ASSOC);
    return $query->fetch();
}

/**
 * Returns a numerical array that corresponds to the fetched row and moves the internal data pointer ahead. 
 * @param resource $query The result resource that is being evaluated. This result comes from a call to dbquery().
 * @return array Returns an numerical array of strings that corresponds to the fetched row, or FALSE if there are no more rows. 
 */
function dbarraynum($query) {
    global $pdo;
    $query->setFetchMode(PDO::FETCH_NUM);
    return $query->fetch();
}

/**
 * Returns the ID of the last inserted row or sequence value
 * @return string $id Returns the ID of the last inserted row, or the last value from a sequence object, depending on the underlying driver.
 */
function dblastid() {
    global $pdo;
    $id = $pdo->lastInsertId();
    return $id;
}

/**
 * Check if a table exists
 * @param string $table
 * @return boolean If table exists (TRUE) or not (FALSE)
 */
function dbexists($table) {
    global $pdo;

    $results = dbquery("SHOW TABLES LIKE '$table'");
    if (!$results) {
        return false;
    }
    if (dbrows($results) > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * dbUnbufferedQuery sends an unique unbuffered query to the database.
 * @param resource $query A SQL query, The query string should not end with a semicolon.
 * @param array $execute
 * @return mixed FALSE or an array $result
 */
function dbUnbufferedQuery($query, $execute = array()) {
    global $pdo;

    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    $ret = dbquery($query, $execute = array());
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

    return $ret;
}

?>
