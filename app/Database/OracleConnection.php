<?php

namespace App\Database;

use Yajra\Oci8\Oci8Connection;

class OracleConnection extends Oci8Connection
{
    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $results = parent::select($query, $bindings, $useReadPdo);
        return $this->normalizeCase($results);
    }

    /**
     * Run a select statement against the database on the write connection.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return array
     */
    public function selectFromWriteConnection($query, $bindings = [])
    {
        $results = parent::selectFromWriteConnection($query, $bindings);
        return $this->normalizeCase($results);
    }

    /**
     * Normalize query result keys to support both lowercase and camelCase properties.
     *
     * @param  mixed  $results
     * @return mixed
     */
    protected function normalizeCase($results)
    {
        if (!is_array($results)) {
            return $results;
        }

        foreach ($results as $row) {
            if (is_object($row)) {
                foreach (get_object_vars($row) as $key => $val) {
                    // Read stream resources (like CLOBs) into strings for Blade compatibility
                    if (is_resource($val)) {
                        $val = stream_get_contents($val);
                        $row->$key = $val;
                    }

                    $lowerKey = strtolower($key);
                    
                    // Map uppercase or mixed-case properties to lowercase
                    if ($key !== $lowerKey) {
                        $row->$lowerKey = $val;
                    }
                    
                    // Map specific lowercase keys back to camelCase for views
                    if ($lowerKey === 'rolename') {
                        $row->roleName = $val;
                    }
                    if ($lowerKey === 'agencyname') {
                        $row->agencyName = $val;
                    }
                    if ($lowerKey === 'areaname') {
                        $row->areaName = $val;
                    }
                }
            }
        }

        return $results;
    }
}

