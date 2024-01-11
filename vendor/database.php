<?php

/**
 * Execute a SQL query against the connection.
 *
 * The PDO statement and boolean result will be returned in an array.
 *
 * @param  string  $sql
 * @param  array   $bindings
 * @return array
 */
function execute_filter_bindings($binding)
{
    return !$binding instanceof Database_Exception && !$binding instanceof Expression;
}

abstract class Grammar
{

    /**
     * The keyword identifier for the database system.
     *
     * @var string
     */
    protected $wrapper = '"%s"';

    /**
     * The database connection instance for the grammar.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Create a new database grammar instance.
     *
     * @param  Connection  $connection
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  string  $table
     * @return string
     */
    public function wrap_table($table)
    {
        // Expressions should be injected into the query as raw strings
        // so we do not want to wrap them in any way. We will just return
        // the string value from the expression to be included.
        if ($table instanceof Expression) {
            return $this->wrap($table);
        }

        $prefix = '';

        // Tables may be prefixed with a string. This allows developers to
        // prefix tables by application on the same database which may be
        // required in some brown-field situations.
        if (isset($this->connection->config['prefix'])) {
            $prefix = $this->connection->config['prefix'];
        }

        return $this->wrap($prefix . $table);
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    public function wrap($value)
    {
        // Expressions should be injected into the query as raw strings
        // so we do not want to wrap them in any way. We will just return
        // the string value from the expression to be included.
        if ($value instanceof Expression) {
            return $value->get();
        }

        // If the value being wrapped contains a column alias, we need to
        // wrap it a little differently as each segment must be wrapped
        // and not the entire string.
        if (strpos(strtolower($value), ' as ') !== false) {
            $segments = explode(' ', $value);

            return sprintf(
                '%s AS %s',
                $this->wrap($segments[0]),
                $this->wrap($segments[2])
            );
        }

        // Since columns may be prefixed with their corresponding table
        // name so as to not make them ambiguous, we will need to wrap
        // the table and the column in keyword identifiers.
        $segments = explode('.', $value);

        foreach ($segments as $key => $value) {
            if ($key == 0 and count($segments) > 1) {
                $wrapped[] = $this->wrap_table($value);
            } else {
                $wrapped[] = $this->wrap_value($value);
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * Wrap a single string value in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrap_value($value)
    {
        return ($value !== '*') ? ($value ? sprintf($this->wrapper, $value) : null) : $value;
    }

    /**
     * Create query parameters from an array of values.
     *
     * <code>
     *      Returns "?, ?, ?", which may be used as PDO place-holders
     *      $parameters = $grammar->parameterize(array(1, 2, 3));
     *
     *      // Returns "?, "Taylor"" since an expression is used
     *      $parameters = $grammar->parameterize(array(1, DB::raw('Taylor')));
     * </code>
     *
     * @param  array   $values
     * @return string
     */
    final public function parameterize($values)
    {
        return implode(', ', array_map(array($this, 'parameter'), $values));
    }

    /**
     * Get the appropriate query parameter string for a value.
     *
     * <code>
     *      // Returns a "?" PDO place-holder
     *      $value = $grammar->parameter('Taylor Otwell');
     *
     *      // Returns "Taylor Otwell" as the raw value of the expression
     *      $value = $grammar->parameter(DB::raw('Taylor Otwell'));
     * </code>
     *
     * @param  mixed   $value
     * @return string
     */
    final public function parameter($value)
    {
        return ($value instanceof Expression) ? $value->get() : '?';
    }

    /**
     * Create a comma-delimited list of wrapped column names.
     *
     * <code>
     *      // Returns ""Taylor", "Otwell"" when the identifier is quotes
     *      $columns = $grammar->columnize(array('Taylor', 'Otwell'));
     * </code>
     *
     * @param  array   $columns
     * @return string
     */
    final public function columnize($columns)
    {
        return implode(', ', array_map(array($this, 'wrap'), $columns));
    }
}

class Query_Grammar extends Grammar
{

    /**
     * The format for properly saving a DateTime.
     *
     * @var string
     */
    public $datetime = 'Y-m-d H:i:s';

    /**
     * All of the query components in the order they should be built.
     *
     * @var array
     */
    protected $components = array(
        'aggregate', 'selects', 'from', 'joins', 'wheres',
        'groupings', 'havings', 'orderings', 'limit', 'offset',
    );

    /**
     * Compile a SQL SELECT statement from a Query instance.
     *
     * @param  Query   $query
     * @return string
     */
    public function select($query)
    {
        return $this->concatenate($this->components($query));
    }

    /**
     * Generate the SQL for every component of the query.
     *
     * @param  Query  $query
     * @return array
     */
    final protected function components($query)
    {
        // Each portion of the statement is compiled by a function corresponding
        // to an item in the components array. This lets us to keep the creation
        // of the query very granular and very flexible.
        foreach ($this->components as $component) {
            if (!is_null($query->$component)) {
                $sql[$component] = call_user_func(array($this, $component), $query);
            }
        }

        return (array) $sql;
    }

    /**
     * Concatenate an array of SQL segments, removing those that are empty.
     *
     * @param  array   $components
     * @return string
     */
    protected function concatenate_filter($value)
    {
        return (string) $value !== '';
    }

    final protected function concatenate($components)
    {
        return implode(' ', array_filter($components, array($this, 'concatenate_filter')));
    }

    /**
     * Compile the SELECT clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function selects($query)
    {
        if (!is_null($query->aggregate)) return;

        $select = ($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

        return $select . $this->columnize($query->selects);
    }

    /**
     * Compile an aggregating SELECT clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function aggregate($query)
    {
        $column = $this->columnize($query->aggregate['columns']);

        // If the "distinct" flag is set and we're not aggregating everything
        // we'll set the distinct clause on the query, since this is used
        // to count all of the distinct values in a column, etc.
        if ($query->distinct and $column !== '*') {
            $column = 'DISTINCT ' . $column;
        }

        return 'SELECT ' . $query->aggregate['aggregator'] . '(' . $column . ') AS ' . $this->wrap('aggregate');
    }

    /**
     * Compile the FROM clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function from($query)
    {
        return 'FROM ' . $this->wrap_table($query->from);
    }

    /**
     * Compile the JOIN clauses for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function joins($query)
    {
        // We need to iterate through each JOIN clause that is attached to the
        // query and translate it into SQL. The table and the columns will be
        // wrapped in identifiers to avoid naming collisions.
        foreach ($query->joins as $join) {
            $table = $this->wrap_table($join->table);

            $clauses = array();

            // Each JOIN statement may have multiple clauses, so we will iterate
            // through each clause creating the conditions then we'll join all
            // of them together at the end to build the clause.
            foreach ($join->clauses as $clause) {
                extract($clause);

                $column1 = $this->wrap($column1);

                $column2 = $this->wrap($column2);

                $clauses[] = "{$connector} {$column1} {$operator} {$column2}";
            }

            // The first clause will have a connector on the front, but it is
            // not needed on the first condition, so we will strip it off of
            // the condition before adding it to the array of joins.
            $search = array('AND ', 'OR ');

            $clauses[0] = str_replace($search, '', $clauses[0]);

            $clauses = implode(' ', $clauses);

            $sql[] = "{$join->type} JOIN {$table} ON {$clauses}";
        }

        // Finally, we should have an array of JOIN clauses that we can
        // implode together and return as the complete SQL for the
        // join clause of the query under construction.
        return implode(' ', $sql);
    }

    /**
     * Compile the WHERE clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    final protected function wheres($query)
    {
        if (is_null($query->wheres)) return '';

        // Each WHERE clause array has a "type" that is assigned by the query
        // builder, and each type has its own compiler function. We will call
        // the appropriate compiler for each where clause.
        foreach ($query->wheres as $where) {
            $sql[] = $where['connector'] . ' ' . $this->{$where['type']}($where);
        }

        if (isset($sql)) {
            // We attach the boolean connector to every where segment just
            // for convenience. Once we have built the entire clause we'll
            // remove the first instance of a connector.
            return 'WHERE ' . preg_replace('/AND |OR /', '', implode(' ', $sql), 1);
        }
    }

    /**
     * Compile a nested WHERE clause.
     *
     * @param  array   $where
     * @return string
     */
    protected function where_nested($where)
    {
        return '(' . substr($this->wheres($where['query']), 6) . ')';
    }

    /**
     * Compile a simple WHERE clause.
     *
     * @param  array   $where
     * @return string
     */
    protected function where($where)
    {
        $parameter = $this->parameter($where['value']);

        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' ' . $parameter;
    }

    /**
     * Compile a WHERE IN clause.
     *
     * @param  array   $where
     * @return string
     */
    protected function where_in($where)
    {
        $parameters = $this->parameterize($where['values']);

        return $this->wrap($where['column']) . ' IN (' . $parameters . ')';
    }

    /**
     * Compile a WHERE NOT IN clause.
     *
     * @param  array   $where
     * @return string
     */
    protected function where_not_in($where)
    {
        $parameters = $this->parameterize($where['values']);

        return $this->wrap($where['column']) . ' NOT IN (' . $parameters . ')';
    }

    /**
     * Compile a WHERE BETWEEN clause
     *  
     * @param  array  $where
     * @return string
     */
    protected function where_between($where)
    {
        $min = $this->parameter($where['min']);
        $max = $this->parameter($where['max']);

        return $this->wrap($where['column']) . ' BETWEEN ' . $min . ' AND ' . $max;
    }

    /**
     * Compile a WHERE NOT BETWEEN clause
     * @param  array $where 
     * @return string        
     */
    protected function where_not_between($where)
    {
        $min = $this->parameter($where['min']);
        $max = $this->parameter($where['max']);

        return $this->wrap($where['column']) . ' NOT BETWEEN ' . $min . ' AND ' . $max;
    }

    /**
     * Compile a WHERE NULL clause.
     *
     * @param  array   $where
     * @return string
     */
    protected function where_null($where)
    {
        return $this->wrap($where['column']) . ' IS NULL';
    }

    /**
     * Compile a WHERE NULL clause.
     *
     * @param  array   $where
     * @return string
     */
    protected function where_not_null($where)
    {
        return $this->wrap($where['column']) . ' IS NOT NULL';
    }

    /**
     * Compile a raw WHERE clause.
     *
     * @param  array   $where
     * @return string
     */
    final protected function where_raw($where)
    {
        return $where['sql'];
    }

    /**
     * Compile the GROUP BY clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function groupings($query)
    {
        return 'GROUP BY ' . $this->columnize($query->groupings);
    }

    /**
     * Compile the HAVING clause for a query.
     *
     * @param  Query  $query
     * @return string
     */
    protected function havings($query)
    {
        if (is_null($query->havings)) return '';

        foreach ($query->havings as $having) {
            $sql[] = 'AND ' . $this->wrap($having['column']) . ' ' . $having['operator'] . ' ' . $this->parameter($having['value']);
        }

        return 'HAVING ' . preg_replace('/AND /', '', implode(' ', $sql), 1);
    }

    /**
     * Compile the ORDER BY clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function orderings($query)
    {
        foreach ($query->orderings as $ordering) {
            $sql[] = $this->wrap($ordering['column']) . ' ' . strtoupper($ordering['direction']);
        }

        return 'ORDER BY ' . implode(', ', $sql);
    }

    /**
     * Compile the LIMIT clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function limit($query)
    {
        return 'LIMIT ' . $query->limit;
    }

    /**
     * Compile the OFFSET clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function offset($query)
    {
        return 'OFFSET ' . $query->offset;
    }

    /**
     * Compile a SQL INSERT statement from a Query instance.
     *
     * This method handles the compilation of single row inserts and batch inserts.
     *
     * @param  Query   $query
     * @param  array   $values
     * @return string
     */
    public function insert($query, $values)
    {
        $table = $this->wrap_table($query->from);

        // Force every insert to be treated like a batch insert. This simply makes
        // creating the SQL syntax a little easier on us since we can always treat
        // the values as if it contains multiple inserts.
        if (!is_array(reset($values))) $values = array($values);

        // Since we only care about the column names, we can pass any of the insert
        // arrays into the "columnize" method. The columns should be the same for
        // every record inserted into the table.
        $columns = $this->columnize(array_keys(reset($values)));

        // Build the list of parameter place-holders of values bound to the query.
        // Each insert should have the same number of bound parameters, so we can
        // just use the first array of values.
        $parameters = $this->parameterize(reset($values));

        $parameters = implode(', ', array_fill(0, count($values), "($parameters)"));

        return "INSERT INTO {$table} ({$columns}) VALUES {$parameters}";
    }

    /**
     * Compile a SQL INSERT and get ID statement from a Query instance.
     *
     * @param  Query   $query
     * @param  array   $values
     * @param  string  $column
     * @return string
     */
    public function insert_get_id($query, $values, $column)
    {
        return $this->insert($query, $values);
    }

    /**
     * Compile a SQL UPDATE statement from a Query instance.
     *
     * @param  Query   $query
     * @param  array   $values
     * @return string
     */
    public function update($query, $values)
    {
        $table = $this->wrap_table($query->from);

        // Each column in the UPDATE statement needs to be wrapped in the keyword
        // identifiers, and a place-holder needs to be created for each value in
        // the array of bindings, so we'll build the sets first.
        foreach ($values as $column => $value) {
            $columns[] = $this->wrap($column) . ' = ' . $this->parameter($value);
        }

        $columns = implode(', ', $columns);

        // UPDATE statements may be constrained by a WHERE clause, so we'll run
        // the entire where compilation process for those constraints. This is
        // easily achieved by passing it to the "wheres" method.
        return trim("UPDATE {$table} SET {$columns} " . $this->wheres($query));
    }

    /**
     * Compile a SQL DELETE statement from a Query instance.
     *
     * @param  Query   $query
     * @return string
     */
    public function delete($query)
    {
        $table = $this->wrap_table($query->from);

        return trim("DELETE FROM {$table} " . $this->wheres($query));
    }

    /**
     * Transform an SQL short-cuts into real SQL for PDO.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return string
     */
    public function shortcut($sql, &$bindings)
    {
        // Laravel provides an easy short-cut notation for writing raw WHERE IN
        // statements. If (...) is in the query, it will be replaced with the
        // correct number of parameters based on the query bindings.
        if (strpos($sql, '(...)') !== false) {
            for ($i = 0; $i < count($bindings); $i++) {
                // If the binding is an array, we can just assume it's used to fill a
                // where in condition, so we'll just replace the next place-holder
                // in the query with the constraint and splice the bindings.
                if (is_array($bindings[$i])) {
                    $parameters = $this->parameterize($bindings[$i]);

                    array_splice($bindings, $i, 1, $bindings[$i]);

                    $sql = preg_replace('~\(\.\.\.\)~', "({$parameters})", $sql, 1);
                }
            }
        }

        return trim($sql);
    }
}

class Query_Grammar_SQLServer extends Query_Grammar
{

    /**
     * The keyword identifier for the database system.
     *
     * @var string
     */
    protected $wrapper = '[%s]';

    /**
     * The format for properly saving a DateTime.
     *
     * @var string
     */
    public $datetime = 'Y-m-d H:i:s.000';

    /**
     * Compile a SQL SELECT statement from a Query instance.
     *
     * @param  Query   $query
     * @return string
     */
    public function select($query)
    {
        $sql = parent::components($query);

        // SQL Server does not currently implement an "OFFSET" type keyword, so we
        // actually have to generate the ANSI standard SQL for doing offset like
        // functionality. OFFSET is in SQL Server 2012, however.
        if ($query->offset > 0) {
            return $this->ansi_offset($query, $sql);
        }

        // Once all of the clauses have been compiled, we can join them all as
        // one statement. Any segments that are null or an empty string will
        // be removed from the array before imploding.
        return $this->concatenate($sql);
    }

    /**
     * Compile the SELECT clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function selects($query)
    {
        if (!is_null($query->aggregate)) return;

        $select = ($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

        // Instead of using a "LIMIT" keyword, SQL Server uses the TOP keyword
        // within the SELECT statement. So, if we have a limit, we will add
        // it to the query here if there is not an OFFSET present.
        if ($query->limit > 0 and $query->offset <= 0) {
            $select .= 'TOP ' . $query->limit . ' ';
        }

        return $select . $this->columnize($query->selects);
    }

    /**
     * Generate the ANSI standard SQL for an offset clause.
     *
     * @param  Query  $query
     * @param  array  $components
     * @return array
     */
    protected function ansi_offset($query, $components)
    {
        // An ORDER BY clause is required to make this offset query work, so if
        // one doesn't exist, we'll just create a dummy clause to trick the
        // database and pacify it so it doesn't complain about the query.
        if (!isset($components['orderings'])) {
            $components['orderings'] = 'ORDER BY (SELECT 0)';
        }

        // We need to add the row number to the query so we can compare it to
        // the offset and limit values given for the statement. So we'll add
        // an expression to the select for the row number.
        $orderings = $components['orderings'];

        $components['selects'] .= ", ROW_NUMBER() OVER ({$orderings}) AS RowNum";

        unset($components['orderings']);

        $start = $query->offset + 1;

        // Next we need to calculate the constraint that should be placed on
        // the row number to get the correct offset and limit on the query.
        // If there is not a limit, we'll just handle the offset.
        if ($query->limit > 0) {
            $finish = $query->offset + $query->limit;

            $constraint = "BETWEEN {$start} AND {$finish}";
        } else {
            $constraint = ">= {$start}";
        }

        // We're finally ready to build the final SQL query so we'll create
        // a common table expression with the query and select all of the
        // results with row numbers between the limit and offset.
        $sql = $this->concatenate($components);

        return "SELECT * FROM ($sql) AS TempTable WHERE RowNum {$constraint}";
    }

    /**
     * Compile the LIMIT clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function limit($query)
    {
        return '';
    }

    /**
     * Compile the OFFSET clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function offset($query)
    {
        return '';
    }
}

class Query_Grammar_SQLite extends Query_Grammar
{

    /**
     * Compile the ORDER BY clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function orderings($query)
    {
        foreach ($query->orderings as $ordering) {
            $sql[] = $this->wrap($ordering['column']) . ' COLLATE NOCASE ' . strtoupper($ordering['direction']);
        }

        return 'ORDER BY ' . implode(', ', $sql);
    }

    /**
     * Compile a SQL INSERT statement from a Query instance.
     *
     * This method handles the compilation of single row inserts and batch inserts.
     *
     * @param  Query   $query
     * @param  array   $values
     * @return string
     */
    public function insert($query, $values)
    {
        // Essentially we will force every insert to be treated as a batch insert which
        // simply makes creating the SQL easier for us since we can utilize the same
        // basic routine regardless of an amount of records given to us to insert.
        $table = $this->wrap_table($query->from);

        if (!is_array(reset($values))) {
            $values = array($values);
        }

        // If there is only one record being inserted, we will just use the usual query
        // grammar insert builder because no special syntax is needed for the single
        // row inserts in SQLite. However, if there are multiples, we'll continue.
        if (count($values) == 1) {
            return parent::insert($query, $values[0]);
        }

        $names = $this->columnize(array_keys($values[0]));

        $columns = array();

        // SQLite requires us to build the multi-row insert as a listing of select with
        // unions joining them together. So we'll build out this list of columns and
        // then join them all together with select unions to complete the queries.
        foreach (array_keys($values[0]) as $column) {
            $columns[] = '? AS ' . $this->wrap($column);
        }

        $columns = array_fill(9, count($values), implode(', ', $columns));

        return "INSERT INTO $table ($names) SELECT " . implode(' UNION SELECT ', $columns);
    }
}

class Query_Grammar_Postgres extends Query_Grammar
{

    /**
     * Compile a SQL INSERT and get ID statement from a Query instance.
     *
     * @param  Query   $query
     * @param  array   $values
     * @param  string  $column
     * @return string
     */
    public function insert_get_id($query, $values, $column)
    {
        return $this->insert($query, $values) . " RETURNING $column";
    }
}

class Query_Grammar_MySQL extends Query_Grammar
{

    /**
     * The keyword identifier for the database system.
     *
     * @var string
     */
    protected $wrapper = '`%s`';
}

abstract class Connector
{

    /**
     * The PDO connection options.
     *
     * @var array
     */
    protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    /**
     * Establish a PDO database connection.
     *
     * @param  array  $config
     * @return PDO
     */
    abstract public function connect($config);

    /**
     * Get the PDO connection options for the configuration.
     *
     * Developer specified options will override the default connection options.
     *
     * @param  array  $config
     * @return array
     */
    protected function options($config)
    {
        $options = (isset($config['options'])) ? $config['options'] : array();

        return array_diff_key($this->options, $options) + $options;
    }
}

class Connector_SQLServer extends Connector
{

    /**
     * The PDO connection options.
     *
     * @var array
     */
    protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    );

    /**
     * Establish a PDO database connection.
     *
     * @param  array  $config
     * @return PDO
     */
    public function connect($config)
    {
        extract($config);

        // Format the SQL Server connection string. This connection string format can
        // also be used to connect to Azure SQL Server databases. The port is defined
        // directly after the server name, so we'll create that first.
        $port = (isset($port)) ? ',' . $port : '';

        //check for dblib for mac users connecting to mssql (utilizes freetds)
        if (in_array('dblib', PDO::getAvailableDrivers())) {
            $dsn = "dblib:host={$host}{$port};dbname={$database}";
        } else {
            $dsn = "sqlsrv:Server={$host}{$port};Database={$database}";
        }

        return new PDO($dsn, $username, $password, $this->options($config));
    }
}

class Connector_SQLite extends Connector
{

    /**
     * Establish a PDO database connection.
     *
     * @param  array  $config
     * @return PDO
     */
    public function connect($config)
    {
        $options = $this->options($config);

        // SQLite provides supported for "in-memory" databases, which exist only for
        // lifetime of the request. Any given in-memory database may only have one
        // PDO connection open to it at a time. These are mainly for tests.
        if ($config['database'] == ':memory:') {
            return new PDO('sqlite::memory:', null, null, $options);
        }

        $path = STORAGE_PATH . 'database' . DS . $config['database'] . '.sqlite';

        return new PDO('sqlite:' . $path, null, null, $options);
    }
}

class Connector_Postgres extends Connector
{

    /**
     * The PDO connection options.
     *
     * @var array
     */
    protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    );

    /**
     * Establish a PDO database connection.
     *
     * @param  array  $config
     * @return PDO
     */
    public function connect($config)
    {
        extract($config);

        $host_dsn = isset($host) ? 'host=' . $host . ';' : '';

        $dsn = "pgsql:{$host_dsn}dbname={$database}";

        // The developer has the freedom of specifying a port for the PostgresSQL
        // database or the default port (5432) will be used by PDO to create the
        // connection to the database for the developer.
        if (isset($config['port'])) {
            $dsn .= ";port={$config['port']}";
        }

        $connection = new PDO($dsn, $username, $password, $this->options($config));

        // If a character set has been specified, we'll execute a query against
        // the database to set the correct character set. By default, this is
        // set to UTF-8 which should be fine for most scenarios.
        if (isset($config['charset'])) {
            $connection->prepare("SET NAMES '{$config['charset']}'")->execute();
        }

        // If a schema has been specified, we'll execute a query against
        // the database to set the search path.
        if (isset($config['schema'])) {
            $connection->prepare("SET search_path TO {$config['schema']}")->execute();
        }

        return $connection;
    }
}

class Connector_MySQL extends Connector
{

    /**
     * Establish a PDO database connection.
     *
     * @param  array  $config
     * @return PDO
     */
    public function connect($config)
    {
        extract($config);

        $dsn = "mysql:host={$host};dbname={$database}";

        // The developer has the freedom of specifying a port for the MySQL database
        // or the default port (3306) will be used to make the connection by PDO.
        // The Unix socket may also be specified if necessary.
        if (isset($config['port'])) {
            $dsn .= ";port={$config['port']}";
        }

        // The UNIX socket option allows the developer to indicate that the MySQL
        // instance must be connected to via a given socket. We'll just append
        // it to the DSN connection string if it is present.
        if (isset($config['unix_socket'])) {
            $dsn .= ";unix_socket={$config['unix_socket']}";
        }

        $connection = new PDO($dsn, $username, $password, $this->options($config));

        // If a character set has been specified, we'll execute a query against
        // the database to set the correct character set. By default, this is
        // set to UTF-8 which should be fine for most scenarios.
        if (isset($config['charset'])) {
            $connection->prepare("SET NAMES '{$config['charset']}'")->execute();
        }

        return $connection;
    }
}

class Query_Join
{

    /**
     * The type of join being performed.
     *
     * @var string
     */
    public $type;

    /**
     * The table the join clause is joining to.
     *
     * @var string
     */
    public $table;

    /**
     * The ON clauses for the join.
     *
     * @var array
     */
    public $clauses = array();

    /**
     * Create a new query join instance.
     *
     * @param  string  $type
     * @param  string  $table
     * @return void
     */
    public function __construct($type, $table)
    {
        $this->type = $type;
        $this->table = $table;
    }

    /**
     * Add an ON clause to the join.
     *
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @param  string  $connector
     * @return Join
     */
    public function on($column1, $operator, $column2, $connector = 'AND')
    {
        $this->clauses[] = compact('column1', 'operator', 'column2', 'connector');

        return $this;
    }

    /**
     * Add an OR ON clause to the join.
     *
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @return Join
     */
    public function or_on($column1, $operator, $column2)
    {
        return $this->on($column1, $operator, $column2, 'OR');
    }
}

class Query
{

    /**
     * The database connection.
     *
     * @var Connection
     */
    public $connection;

    /**
     * The query grammar instance.
     *
     * @var Query\Grammars\Grammar
     */
    public $grammar;

    /**
     * The SELECT clause.
     *
     * @var array
     */
    public $selects;

    /**
     * The aggregating column and function.
     *
     * @var array
     */
    public $aggregate;

    /**
     * Indicates if the query should return distinct results.
     *
     * @var bool
     */
    public $distinct = false;

    /**
     * The table name.
     *
     * @var string
     */
    public $from;

    /**
     * The table joins.
     *
     * @var array
     */
    public $joins;

    /**
     * The WHERE clauses.
     *
     * @var array
     */
    public $wheres;

    /**
     * The GROUP BY clauses.
     *
     * @var array
     */
    public $groupings;

    /**
     * The HAVING clauses.
     *
     * @var array
     */
    public $havings;

    /**
     * The ORDER BY clauses.
     *
     * @var array
     */
    public $orderings;

    /**
     * The LIMIT value.
     *
     * @var int
     */
    public $limit;

    /**
     * The OFFSET value.
     *
     * @var int
     */
    public $offset;

    /**
     * The query value bindings.
     *
     * @var array
     */
    public $bindings = array();

    /**
     * Create a new query instance.
     *
     * @param  Connection  $connection
     * @param  Grammar     $grammar
     * @param  string      $table
     * @return void
     */
    public function __construct($connection, $grammar, $table)
    {
        $this->from = $table;
        $this->grammar = $grammar;
        $this->connection = $connection;
    }

    /**
     * Force the query to return distinct results.
     *
     * @return Query
     */
    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Add an array of columns to the SELECT clause.
     *
     * @param  array  $columns
     * @return Query
     */
    public function select($columns = array('*'))
    {
        $this->selects = (array) $columns;
        return $this;
    }

    /**
     * Add a join clause to the query.
     *
     * @param  string  $table
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @param  string  $type
     * @return Query
     */
    public function join($table, $column1, $operator = null, $column2 = null, $type = 'INNER')
    {
        // If the "column" is really an instance of a Closure, the developer is
        // trying to create a join with a complex "ON" clause. So, we will add
        // the join, and then call the Closure with the join/

        if ($column1 instanceof Closure) {
            $this->joins[] = new Query_Join($type, $table);

            call_user_func($column1, end($this->joins));
        } elseif (is_array($column1)) {
            $join = new Query_Join($type, $table);
            foreach ($column1 as $_join) {
                list($_column1, $_operator, $_column2) = $_join;
                $join->on($_column1, $_operator, $_column2);
            }
            $this->joins[] = $join;
        }

        // If the column is just a string, we can assume that the join just
        // has a simple on clause, and we'll create the join instance and
        // add the clause automatically for the develoepr.
        else {
            $join = new Query_Join($type, $table);

            $join->on($column1, $operator, $column2);

            $this->joins[] = $join;
        }

        /*$join = new Query_Join($type, $table);

        $join->on($column1, $operator, $column2);

        $this->joins[] = $join;*/

        return $this;
    }

    /**
     * Add a left join to the query.
     *
     * @param  string  $table
     * @param  string  $column1
     * @param  string  $operator
     * @param  string  $column2
     * @return Query
     */
    public function left_join($table, $column1, $operator = null, $column2 = null)
    {
        return $this->join($table, $column1, $operator, $column2, 'LEFT');
    }

    /**
     * Reset the where clause to its initial state.
     *
     * @return void
     */
    public function reset_where()
    {
        list($this->wheres, $this->bindings) = array(array(), array());
    }

    /**
     * Add a raw where condition to the query.
     *
     * @param  string  $where
     * @param  array   $bindings
     * @param  string  $connector
     * @return Query
     */
    public function raw_where($where, $bindings = array(), $connector = 'AND')
    {
        $this->wheres[] = array('type' => 'where_raw', 'connector' => $connector, 'sql' => $where);

        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * Add a raw or where condition to the query.
     *
     * @param  string  $where
     * @param  array   $bindings
     * @return Query
     */
    public function raw_or_where($where, $bindings = array())
    {
        return $this->raw_where($where, $bindings, 'OR');
    }

    /**
     * Add a where condition to the query.
     *
     * <code>
     * // Simpes Where
     * $query->where('column', '=', 'value'[, 'AND/OR']);
     * 
     * // Multi Clauses and Where Nested
     * $query->where(array(
     *      array('column', '=', 'value'),
     *      array('column2', '=', 'value2'),
     *      array('column3', '=', 'value3'[, 'AND/OR'])
     * )[, true]);
     * // Set the second parameter TRUE for nested where clause
     * // WHERE (column = value AND column2 = value2)
     * </code>
     * 
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $connector
     * @return Query
     */
    public function where($column, $operator = null, $value = null, $connector = 'AND')
    {
        if (is_array($column)) {
            // Nested Where
            if ($operator === true) {
                $this->where_nested($column, $connector);
            } else {
                foreach ($column as $key => $where) {
                    @list($_column, $_operator, $_value, $_connector) = $where;
                    if (is_null($_connector)) {
                        $_connector = is_null($operator) ? $connector : $operator;
                    }
                    $this->where($_column, $_operator, $_value, $_connector);
                }
            }

            return $this;
        }

        // If a Closure is passed into the method, it means a nested where
        // clause is being initiated, so we will take a different course
        // of action than when the statement is just a simple where.
        if ($column instanceof Closure) {
            return $this->where_nested($column, $connector);
        }

        $args = func_get_args();

        if (count($args) == 2 || $value === null) {
            $value = $operator;
            $operator = '=';
        }

        $type = 'where';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'connector');

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an or where condition to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return Query
     */
    public function or_where($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Add an or where condition for the primary key to the query.
     *
     * @param  mixed  $value
     * @return Query
     */
    public function or_where_id($value)
    {
        return $this->or_where('id', '=', $value);
    }

    /**
     * Add a where in condition to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $connector
     * @param  bool    $not
     * @return Query
     */
    public function where_in($column, $values, $connector = 'AND', $not = false)
    {
        $type = ($not) ? 'where_not_in' : 'where_in';

        $this->wheres[] = compact('type', 'column', 'values', 'connector');

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Add an or where in condition to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return Query
     */
    public function or_where_in($column, $values)
    {
        return $this->where_in($column, $values, 'OR');
    }

    /**
     * Add a where not in condition to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $connector
     * @return Query
     */
    public function where_not_in($column, $values, $connector = 'AND')
    {
        return $this->where_in($column, $values, $connector, true);
    }

    /**
     * Add an or where not in condition to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return Query
     */
    public function or_where_not_in($column, $values)
    {
        return $this->where_not_in($column, $values, 'OR');
    }

    /**
     * Add a BETWEEN condition to the query
     * 
     * @param  string  $column    
     * @param  mixed  $min       
     * @param  mixed  $max       
     * @param  string  $connector 
     * @param  boolean $not       
     * @return Query
     */
    public function where_between($column, $min, $max, $connector = 'AND', $not = false)
    {
        $type = ($not) ? 'where_not_between' : 'where_between';

        $this->wheres[] = compact('type', 'column', 'min', 'max', 'connector');

        $this->bindings[] = $min;
        $this->bindings[] = $max;

        return $this;
    }

    /**
     * Add a OR BETWEEN condition to the query
     * 
     * @param  string  $column    
     * @param  mixed  $min       
     * @param  mixed  $max       
     * @return Query
     */
    public function or_where_between($column, $min, $max)
    {
        return $this->where_between($column, $min, $max, 'OR');
    }

    /**
     * Add a NOT BETWEEN condition to the query
     * 
     * @param  string  $column    
     * @param  mixed  $min       
     * @param  mixed  $max       
     * @return Query
     */
    public function where_not_between($column, $min, $max, $connector = 'AND')
    {
        return $this->where_between($column, $min, $max, $connector, true);
    }

    /**
     * Add a OR NOT BETWEEN condition to the query
     * 
     * @param  string  $column    
     * @param  mixed  $min       
     * @param  mixed  $max       
     * @return Query
     */
    public function or_where_not_between($column, $min, $max)
    {
        return $this->where_not_between($column, $min, $max, 'OR');
    }

    /**
     * Add a where null condition to the query.
     *
     * @param  string  $column
     * @param  string  $connector
     * @param  bool    $not
     * @return Query
     */
    public function where_null($column, $connector = 'AND', $not = false)
    {
        $type = ($not) ? 'where_not_null' : 'where_null';

        $this->wheres[] = compact('type', 'column', 'connector');

        return $this;
    }

    /**
     * Add an or where null condition to the query.
     *
     * @param  string  $column
     * @return Query
     */
    public function or_where_null($column)
    {
        return $this->where_null($column, 'OR');
    }

    /**
     * Add a where not null condition to the query.
     *
     * @param  string  $column
     * @param  string  $connector
     * @return Query
     */
    public function where_not_null($column, $connector = 'AND')
    {
        return $this->where_null($column, $connector, true);
    }

    /**
     * Add an or where not null condition to the query.
     *
     * @param  string  $column
     * @return Query
     */
    public function or_where_not_null($column)
    {
        return $this->where_not_null($column, 'OR');
    }

    /**
     * Add a nested where condition to the query.
     * <code>
     * 
     *  $query->where_nested(array(
     *      array('column', '=', 'value'[, 'AND/OR']),
     *      array('column2', '!=', 'value2'),
     *      array('column3', '>=', 'value3')
     *  )[, 'AND\OR']);
     * 
     * </code>
     * @param  Closure  $callback
     * @param  string   $connector
     * @return Query
     */
    public function where_nested($callback, $connector = 'AND')
    {
        $type = 'where_nested';

        // To handle a nested where statement, we will actually instantiate a new
        // Query instance and run the callback over that instance, which will
        // allow the developer to have a fresh query instance
        $query = new Query($this->connection, $this->grammar, $this->from);

        if ($callback instanceof Closure) {
            call_user_func($callback, $query);
        } else {
            foreach ($callback as $v) {
                list($column, $operator, $value, $_connector) = $v;
                if (is_null($_connector)) $_connector = $connector;
                $query->where($column, $operator, $value, $_connector);
            }
        }

        // Once the callback has been run on the query, we will store the nested
        // query instance on the where clause array so that it's passed to the
        // query's query grammar instance when building.
        if ($query->wheres !== null) {
            $this->wheres[] = compact('type', 'query', 'connector');
        }

        $this->bindings = array_merge($this->bindings, $query->bindings);

        return $this;
    }

    public function like($column, $value, $type = 'both', $connector = 'AND', $not = null)
    {
        $value = str_replace('%', '', $value);
        switch ($type) {
            case 'before':
                $value = "%$value";
                break;

            case 'after':
                $value = "$value%";
                break;

            default:
                $value = "%$value%";
                break;
        }

        return $this->where($column, ($not ? 'NOT ' : '') . 'like', $value, $connector);
    }

    public function or_like($column, $value, $type = 'both')
    {
        return $this->like($column, $value, $type, 'OR');
    }

    public function not_like($column, $value, $type = 'both')
    {
        return $this->like($column, $value, $type, 'AND', true);
    }

    public function or_not_like($column, $value, $type = 'both')
    {
        return $this->like($column, $value, $type, 'OR', true);
    }

    /**
     * Add dynamic where conditions to the query.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return Query
     */
    private function dynamic_where($method, $parameters)
    {
        $finder = substr($method, 6);

        $flags = PREG_SPLIT_DELIM_CAPTURE;

        $segments = preg_split('/(_and_|_or_)/i', $finder, -1, $flags);

        // The connector variable will determine which connector will be used
        // for the condition. We'll change it as we come across new boolean
        // connectors in the dynamic method string.
        //
        // The index variable helps us get the correct parameter value for
        // the where condition. We increment it each time we add another
        // condition to the query's where clause.
        $connector = 'AND';

        $index = 0;

        foreach ($segments as $segment) {
            // If the segment is not a boolean connector, we can assume it it is
            // a column name, and we'll add it to the query as a new constraint
            // of the query's where clause and keep iterating the segments.
            if ($segment != '_and_' and $segment != '_or_') {
                $this->where($segment, '=', $parameters[$index], $connector);

                $index++;
            }
            // Otherwise, we will store the connector so we know how the next
            // where clause we find in the query should be connected to the
            // previous one and will add it when we find the next one.
            else {
                $connector = trim(strtoupper($segment), '_');
            }
        }

        return $this;
    }

    /**
     * Add a grouping to the query.
     *
     * @param  string  $column
     * @return Query
     */
    public function group_by($column)
    {
        $this->groupings[] = $column;
        return $this;
    }

    /**
     * Add a having to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     */
    public function having($column, $operator, $value)
    {
        $this->havings[] = compact('column', 'operator', 'value');

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an ordering to the query.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return Query
     */
    public function order_by($_column, $direction = 'asc')
    {
        if ($_column instanceof Expression) {
            $this->orderings[] = array('type' => 'raw', 'column' => $_column, 'direction' => null);
            return $this;
        }

        $valid_directions = array('asc' => 'ASC', 'desc' => 'DESC');
        $direction = strtoupper(array_get($valid_directions, strtolower($direction), 'ASC'));

        if (!is_array($_column)) {
            $_column = explode(',', preg_replace('/\s+/i', ' ', $_column));
        }
        foreach ($_column as $key => $column) {
            $this->orderings[] = compact('column', 'direction');
        }
        return $this;
    }

    /**
     * Set the query offset.
     *
     * @param  int  $value
     * @return Query
     */
    public function skip($value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * Set the query limit.
     *
     * @param  int  $value
     * @return Query
     */
    public function take($value)
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Set the query limit and offset for a given page.
     *
     * @param  int    $page
     * @param  int    $per_page
     * @return Query
     */
    public function for_page($page, $per_page)
    {
        return $this->skip(($page - 1) * $per_page)->take($per_page);
    }

    /**
     * Find a record by the primary key.
     *
     * @param  int     $id
     * @param  array   $columns
     * @return object
     */
    public function find($id, $columns = array('*'))
    {
        return $this->where('id', '=', $id)->first($columns);
    }

    /**
     * Execute the query as a SELECT statement and return a single column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function only($column)
    {
        $sql = $this->grammar->select($this->select(array($column)));

        return $this->connection->only($sql, $this->bindings);
    }

    /**
     * Execute the query as a SELECT statement and return the first result.
     *
     * @param  array  $columns
     * @return mixed
     */
    public function first($columns = array('*'))
    {
        $columns = (array) $columns;

        // Since we only need the first result, we'll go ahead and set the
        // limit clause to 1, since this will be much faster than getting
        // all of the rows and then only returning the first.
        $results = $this->take(1)->get($columns);

        return (count($results) > 0) ? $results[0] : null;
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param  string  $column
     * @param  string  $key
     * @return array
     */
    public function lists($column, $key = null)
    {
        $columns = (is_null($key)) ? array($column) : array($column, $key);

        $results = $this->get($columns);

        $values = array();

        foreach ($results as $row) {
            $index = (!is_null($key)) ? $row->$key : count($values);
            $values[$index] = $row->$column;
        }

        return $values;
    }

    /**
     * Execute the query as a SELECT statement.
     *
     * @param  array  $columns
     * @return array
     */
    protected function get_walk($result)
    {
        unset($result->rownum);
    }

    public function get($columns = array('*'))
    {
        if (is_null($this->selects)) $this->select($columns);

        $sql = $this->grammar->select($this);

        $results = $this->connection->query($sql, $this->bindings);

        // If the query has an offset and we are using the SQL Server grammar,
        // we need to spin through the results and remove the "rownum" from
        // each of the objects since there is no "offset".
        if ($this->offset > 0 and $this->grammar instanceof Query_Grammar_SQLServer) {
            array_walk($results, array($this, 'get_walk'));
        }

        // Reset the SELECT clause so more queries can be performed using
        // the same instance. This is helpful for getting aggregates and
        // then getting actual results from the query.
        $this->selects = null;

        return $results;
    }

    public function all($columns = array('*'))
    {
        return $this->get($columns);
    }

    public function toSql($columns = array('*'))
    {
        if (is_null($this->selects)) $this->select($columns);

        $sql = $this->grammar->select($this);

        $bindings = (array) $this->bindings;

        // Since expressions are injected into the query as strings, we need to
        // remove them from the array of bindings. After we have removed them,
        // we'll reset the array so there are not gaps within the keys.
        $bindings = array_values(array_filter($bindings, 'execute_filter_bindings'));

        $sql = $this->grammar->shortcut($sql, $bindings);

        // Next we need to translate all DateTime bindings to their date-time
        // strings that are compatible with the database. Each grammar may
        // define it's own date-time format according to its needs.
        $datetime = $this->grammar->datetime;

        for ($i = 0; $i < count($bindings); $i++) {
            if ($bindings[$i] instanceof DateTime) {
                $bindings[$i] = $bindings[$i]->format($datetime);
            }
        }

        $this->selects = null;

        return apply_sql_bindings($sql, $bindings);
    }

    /**
     * Get an aggregate value.
     *
     * @param  string  $aggregator
     * @param  array   $columns
     * @return mixed
     */
    public function aggregate($aggregator, $columns)
    {
        // We'll set the aggregate value so the grammar does not try to compile
        // a SELECT clause on the query. If an aggregator is present, it's own
        // grammar function will be used to build the SQL syntax.
        $this->aggregate = compact('aggregator', 'columns');

        $sql = $this->grammar->select($this);

        $result = $this->connection->only($sql, $this->bindings);

        // Reset the aggregate so more queries can be performed using the same
        // instance. This is helpful for getting aggregates and then getting
        // actual results from the query such as during paging.
        $this->aggregate = null;

        return $result;
    }

    /**
     * Get the paginated query results as a Paginator instance.
     *
     * @param  int        $per_page
     * @param  array      $columns
     * @return Paginator
     */
    public function paginate($per_page = 20, $columns = array('*'))
    {
        // Because some database engines may throw errors if we leave orderings
        // on the query when retrieving the total number of records, we'll drop
        // all of the ordreings and put them back on the query.
        list($orderings, $this->orderings) = array($this->orderings, null);

        $total = $this->count(reset($columns));

        $page = Paginator::page($total, $per_page);

        $this->orderings = $orderings;

        // Now we're ready to get the actual pagination results from the table
        // using the for_page and get methods. The "for_page" method provides
        // a convenient way to set the paging limit and offset.
        $results = $this->for_page($page, $per_page)->get($columns);

        return Paginator::make($results, $total, $per_page);
    }

    /**
     * Insert an array of values into the database table.
     *
     * @param  array  $values
     * @return bool
     */
    public function insert($values)
    {
        $values = (array)$values;
        // Force every insert to be treated like a batch insert to make creating
        // the binding array simpler since we can just spin through the inserted
        // rows as if there/ was more than one every time.
        if (!is_array(reset($values))) $values = array($values);

        $bindings = array();

        // We need to merge the the insert values into the array of the query
        // bindings so that they will be bound to the PDO statement when it
        // is executed by the database connection.
        foreach ($values as $value) {
            $bindings = array_merge($bindings, array_values($value));
        }

        $sql = $this->grammar->insert($this, $values);

        return $this->connection->query($sql, $bindings);
    }

    /**
     * Insert an array of values into the database table and return the key.
     *
     * @param  array   $values
     * @param  string  $column
     * @return mixed
     */
    public function insert_get_id($values, $column = 'id')
    {
        $values = (array)$values;

        $sql = $this->grammar->insert_get_id($this, $values, $column);

        $result = $this->connection->query($sql, array_values($values));

        // If the key is not auto-incrementing, we will just return the inserted value
        if (isset($values[$column])) {
            return $values[$column];
        } else if ($this->grammar instanceof Query_Grammar_Postgres) {
            $row = (array) $result[0];

            return (int) $row[$column];
        } else {
            return (int) $this->connection->pdo->lastInsertId();
        }
    }

    /**
     * Increment the value of a column by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @return int
     */
    public function increment($column, $amount = 1)
    {
        return $this->adjust($column, $amount, ' + ');
    }

    /**
     * Decrement the value of a column by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @return int
     */
    public function decrement($column, $amount = 1)
    {
        return $this->adjust($column, $amount, ' - ');
    }

    /**
     * Adjust the value of a column up or down by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  string  $operator
     * @return int
     */
    protected function adjust($column, $amount, $operator)
    {
        $wrapped = $this->grammar->wrap($column);

        // To make the adjustment to the column, we'll wrap the expression in an
        // Expression instance, which forces the adjustment to be injected into
        // the query as a string instead of bound.
        $value = Database::raw($wrapped . $operator . $amount);

        return $this->update(array($column => $value));
    }

    /**
     * Update an array of values in the database table.
     *
     * @param  array  $values
     * @return int
     */
    public function update($values, $id = null, $pk = 'id')
    {
        if ($id) {
            $this->where($pk, '=', $id);
        }

        $values = (array)$values;
        // For update statements, we need to merge the bindings such that the update
        // values occur before the where bindings in the array since the sets will
        // precede any of the where clauses in the SQL syntax that is generated.
        $bindings =  array_merge(array_values($values), $this->bindings);

        $sql = $this->grammar->update($this, $values);

        return $this->connection->query($sql, $bindings);
    }

    /**
     * Execute the query as a DELETE statement.
     *
     * Optionally, an ID may be passed to the method do delete a specific row.
     *
     * @param  int   $id
     * @return int
     */
    public function delete($id = null)
    {
        // If an ID is given to the method, we'll set the where clause to
        // match on the value of the ID. This allows the developer to
        // quickly delete a row by its primary key value.
        if (!is_null($id)) {
            $this->where('id', '=', $id);
        }

        $sql = $this->grammar->delete($this);

        return $this->connection->query($sql, $this->bindings);
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * This method handles calls to aggregates as well as dynamic where clauses.
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'where_') === 0) {
            return $this->dynamic_where($method, $parameters, $this);
        }

        // All of the aggregate methods are handled by a single method, so we'll
        // catch them all here and then pass them off to the agregate method
        // instead of creating methods for each one of them.
        if (in_array($method, array('count', 'min', 'max', 'avg', 'sum'))) {
            if (count($parameters) == 0) $parameters[0] = '*';

            return $this->aggregate(strtoupper($method), (array) $parameters[0]);
        }

        throw new Exception("Method [$method] is not defined on the Query class.");
    }
}

class Database_Exception extends Exception
{

    /**
     * The inner Database_Exception.
     *
     * @var Database_Exception
     */
    protected $inner;

    /**
     * Create a new database Database_Exception instance.
     *
     * @param  string     $sql
     * @param  array      $bindings
     * @param  Database_Exception  $inner
     * @return void
     */
    public function __construct($sql, $bindings, Exception $inner)
    {
        $this->inner = $inner;

        $this->setMessage($sql, $bindings);

        // Set the Database_Exception code
        $this->code = $inner->getCode();
    }

    /**
     * Get the inner Database_Exception.
     *
     * @return Database_Exception
     */
    public function getInner()
    {
        return $this->inner;
    }

    /**
     * Set the Database_Exception message to include the SQL and bindings.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return void
     */
    protected function setMessage($sql, $bindings)
    {
        $this->message = $this->inner->getMessage();

        $this->message .= "\n\nSQL: " . $sql . "\n\nBindings: " . var_export($bindings, true);
    }
}

class Expression
{

    /**
     * The value of the database expression.
     *
     * @var string
     */
    protected $value;

    /**
     * Create a new database expression instance.
     *
     * @param  string  $value
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the string value of the database expression.
     *
     * @return string
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Get the string value of the database expression.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
}

class Connection
{

    /**
     * The raw PDO connection instance.
     *
     * @var PDO
     */
    public $pdo;

    /**
     * The connection configuration array.
     *
     * @var array
     */
    public $config;

    /**
     * The query grammar instance for the connection.
     *
     * @var Query\Grammars\Grammar
     */
    protected $grammar;

    /**
     * All of the queries that have been executed on all connections.
     *
     * @var array
     */
    public static $queries = array();

    public static $complied_queries = array();

    /**
     * Create a new database connection instance.
     *
     * @param  PDO    $pdo
     * @param  array  $config
     * @return void
     */
    public function __construct($pdo, $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    /**
     * Begin a fluent query against a table.
     *
     * <code>
     *      // Start a fluent query against the "users" table
     *      $query = DB::connection()->table('users');
     *
     *      // Start a fluent query against the "users" table and get all the users
     *      $users = DB::connection()->table('users')->get();
     * </code>
     *
     * @param  string  $table
     * @return Query
     */
    public function table($table)
    {
        return new Query($this, $this->grammar(), $table);
    }

    /**
     * Create a new query grammar for the connection.
     *
     * @return Query\Grammars\Grammar
     */
    protected function grammar()
    {
        if (isset($this->grammar)) return $this->grammar;

        if (isset(Database::$registrar[$this->driver()])) {
            return $this->grammar = Database::$registrar[$this->driver()]['query']();
        }

        $grammar = $this->driver();

        switch ($grammar) {
            case 'mysql':
                $grammar = 'Query_Grammar_MySQL';
                break;

            case 'sqlite':
                $grammar = 'Query_Grammar_SQLite';
                break;

            case 'sqlsrv':
                $grammar = 'Query_Grammar_SQLServer';
                break;

            case 'pgsql':
                $grammar = 'Query_Grammar_Postgres';
                break;

            default:
                $grammar = 'Query_Grammar';
                break;
        }

        return $this->grammar = new $grammar($this);
    }

    /**
     * Execute a callback wrapped in a database transaction.
     *
     * @param  callable  $callback
     * @return bool
     */
    public function transaction(callable $callback)
    {
        $this->pdo->beginTransaction();

        // After beginning the database transaction, we will call the callback
        // so that it can do its database work. If an Database_Exception occurs we'll
        // rollback the transaction and re-throw back to the developer.
        try {
            call_user_func($callback);
        } catch (Database_Exception $e) {
            $this->pdo->rollBack();

            throw $e;
        }

        return $this->pdo->commit();
    }

    /**
     * Execute a SQL query against the connection and return a single column result.
     *
     * <code>
     *      // Get the total number of rows on a table
     *      $count = DB::connection()->only('select count(*) from users');
     *
     *      // Get the sum of payment amounts from a table
     *      $sum = DB::connection()->only('select sum(amount) from payments')
     * </code>
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return mixed
     */
    public function only($sql, $bindings = array())
    {
        $results = (array) $this->first($sql, $bindings);

        return head($results);
    }

    /**
     * Execute a SQL query and return an array of StdClass objects.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return array
     */
    public function query($sql, $bindings = array())
    {
        $sql = trim($sql);

        list($statement, $result) = $this->execute($sql, $bindings);

        // The result we return depends on the type of query executed against the
        // database. On SELECT clauses, we will return the result set, for update
        // and deletes we will return the affected row count.
        if (stripos($sql, 'select') === 0 || stripos($sql, 'show') === 0) {
            return $this->fetch($statement, $GLOBALS['config']['database']['fetch']);
        } elseif (stripos($sql, 'update') === 0 or stripos($sql, 'delete') === 0) {
            return $statement->rowCount();
        }
        // For insert statements that use the "returning" clause, which is allowed
        // by database systems such as Postgres, we need to actually return the
        // real query result so the consumer can get the ID.
        elseif (stripos($sql, 'insert') === 0 and stripos($sql, 'returning') !== false) {
            return $this->fetch($statement, $GLOBALS['config']['database']['fetch']);
        } else {
            return $result;
        }
    }

    /**
     * Execute a SQL query against the connection and return the first result.
     *
     * <code>
     *      // Execute a query against the database connection
     *      $user = DB::connection()->first('select * from users');
     *
     *      // Execute a query with bound parameters
     *      $user = DB::connection()->first('select * from users where id = ?', array($id));
     * </code>
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return object
     */
    public function first($sql, $bindings = array())
    {
        if (count($results = $this->query($sql, $bindings)) > 0) {
            return $results[0];
        }
    }

    protected function execute($sql, $bindings = array())
    {
        $bindings = (array) $bindings;

        // Since expressions are injected into the query as strings, we need to
        // remove them from the array of bindings. After we have removed them,
        // we'll reset the array so there are not gaps within the keys.
        $bindings = array_filter($bindings, 'execute_filter_bindings');

        $bindings = array_values($bindings);

        $sql = $this->grammar()->shortcut($sql, $bindings);

        // Next we need to translate all DateTime bindings to their date-time
        // strings that are compatible with the database. Each grammar may
        // define it's own date-time format according to its needs.
        $datetime = $this->grammar()->datetime;

        for ($i = 0; $i < count($bindings); $i++) {
            if ($bindings[$i] instanceof DateTime) {
                $bindings[$i] = $bindings[$i]->format($datetime);
            }
        }

        // Each database operation is wrapped in a try / catch so we can wrap
        // any database Database_Exceptions in our custom Database_Exception class, which will
        // set the message to include the SQL and query bindings.
        try {
            $statement = $this->pdo->prepare($sql);

            $start = microtime(true);

            $result = $statement->execute($bindings);
        }
        // If an Database_Exception occurs, we'll pass it into our custom Database_Exception
        // and set the message to include the SQL and query bindings so
        // debugging is much easier on the developer.
        catch (Database_Exception $Database_Exception) {
            $Database_Exception = new Database_Exception($sql, $bindings, $Database_Exception);

            throw $Database_Exception;
        }

        // Once we have executed the query, we log the SQL, bindings, and
        // execution time in a static array that is accessed by all of
        // the connections actively being used by the application.
        if ($GLOBALS['config']['database']['profile']) {
            $this->log($sql, $bindings, $start);
        }

        $sql = db_last_query();

        try {
            if (preg_match('#(INSERT|UPDATE|DELETE)(.*)#s', $sql)) {
                // Force log 
                $log_dir = rtrim(ROOT_PATH, DS) . '/storage/logs/sql';
                File::mkdir($log_dir);

                $log_file = $log_dir . DS . date('Ymd') . '.log';

                if (file_exists($log_file)) {
                    @exec('chmod 0777 ' . $log_file);
                }

                if (is_writable($log_file)) {
                    File::append($log_file, date('Y-m-d H:i:s') . ' - ' . json_encode(array(
                        'route' => is_cli() ? array_get($_SERVER, 'SCRIPT_NAME', __FILE__) : urlCurrent(),
                        'sql' => $sql
                    )) . "\n");
                }

                if (file_exists($log_file)) {
                    @exec('chmod 0777 ' . $log_file);
                }
            }
        } catch (\Exception $e) {
            // ...
        }

        return array($statement, $result);
    }

    /**
     * Fetch all of the rows for a given statement.
     *
     * @param  PDOStatement  $statement
     * @param  int           $style
     * @return array
     */
    protected function fetch($statement, $style)
    {
        // If the fetch style is "class", we'll hydrate an array of PHP
        // stdClass objects as generic containers for the query rows,
        // otherwise we'll just use the fetch style value.
        if ($style === PDO::FETCH_CLASS) {
            return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
        } else {
            return $statement->fetchAll($style);
        }
    }

    /**
     * Log the query and fire the core query event.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @param  int     $start
     * @return void
     */
    protected function log($sql, $bindings, $start)
    {
        $time = number_format((microtime(true) - $start) * 1000, 2);

        self::$queries[] = compact('sql', 'bindings', 'time');
    }

    /**
     * Get the driver name for the database connection.
     *
     * @return string
     */
    public function driver()
    {
        return $this->config['driver'];
    }

    /**
     * Magic Method for dynamically beginning queries on database tables.
     */
    public function __call($method, $parameters)
    {
        return $this->table($method);
    }
}

class Database
{

    /**
     * The established database connections.
     *
     * @var array
     */
    public static $connections = array();

    /**
     * The third-party driver registrar.
     *
     * @var array
     */
    public static $registrar = array();

    /**
     * Get a database connection.
     *
     * If no database name is specified, the default connection will be returned.
     *
     * <code>
     *      // Get the default database connection for the application
     *      $connection = DB::connection();
     *
     *      // Get a specific connection by passing the connection name
     *      $connection = DB::connection('mysql');
     * </code>
     *
     * @param  string      $connection
     * @return Database\Connection
     */
    public static function connection($connection = null)
    {
        if (is_null($connection))
            $connection = $GLOBALS['config']['database']['default'];

        if (!isset(self::$connections[$connection])) {
            $config = $GLOBALS['config']['database']['connections'][$connection];

            if (is_null($config)) {
                throw new Exception("Database connection is not defined for [$connection].");
            }

            self::$connections[$connection] = new Connection(self::connect($config), $config);
        }

        return self::$connections[$connection];
    }

    /**
     * Get a PDO database connection for a given database configuration.
     *
     * @param  array  $config
     * @return PDO
     */
    protected static function connect($config)
    {
        return self::connector($config['driver'])->connect($config);
    }

    /**
     * Create a new database connector instance.
     *
     * @param  string     $driver
     * @return Database\Connectors\Connector
     */
    protected static function connector($driver)
    {
        if (isset(self::$registrar[$driver])) {
            $resolver = self::$registrar[$driver]['connector'];

            return $resolver();
        }

        switch ($driver) {
            case 'sqlite':
                $class = 'Connector_SQLite';
                break;

            case 'mysql':
                $class = 'Connector_MySQL';
                break;

            case 'pgsql':
                $class = 'Connector_Postgres';
                break;

            case 'sqlsrv':
                $class = 'Connector_SQLServer';
                break;

            default:
                throw new Exception("Database driver [$driver] is not supported.");
                break;
        }

        return new $class;
    }

    /**
     * Begin a fluent query against a table.
     *
     * @param  string          $table
     * @param  string          $connection
     * @return Database\Query
     */
    public static function table($table, $connection = null)
    {
        return self::connection($connection)->table($table);
    }

    public static function query($sql, $connection = null)
    {
        return self::connection($connection)->query($sql);
    }

    /**
     * Create a new database expression instance.
     *
     * Database expressions are used to inject raw SQL into a fluent query.
     *
     * @param  string      $value
     * @return Expression
     */
    public static function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Escape a string for usage in a query.
     *
     * This uses the correct quoting mechanism for the default database connection.
     *
     * @param  string      $value
     * @return string
     */
    public static function escape($value)
    {
        return self::connection()->pdo->quote($value);
    }

    /**
     * Get the profiling data for all queries.
     *
     * @return array
     */
    public static function profile()
    {
        return Connection::$queries;
    }

    /**
     * Get the last query that was executed.
     *
     * Returns false if no queries have been executed yet.
     *
     * @return string
     */
    public static function last_query($compiled = false)
    {
        $queries = $compiled ? Connection::$complied_queries : Connection::$queries;
        return end($queries);
    }

    /**
     * Register a database connector and grammars.
     *
     * @param  string   $name
     * @param  Closure  $connector
     * @param  Closure  $query
     * @param  Closure  $schema
     * @return void
     */
    public static function extend($name, $connector, $query = null, $schema = null)
    {
        if (is_null($query)) {
            $query = 'Query_Grammar';
        }

        self::$registrar[$name] = compact('connector', 'query', 'schema');
    }

    public static function beginTransaction()
    {
        self::connection()->pdo->beginTransaction();
    }

    public static function rollBack()
    {
        self::connection()->pdo->rollBack();
    }

    public static function commit()
    {
        self::connection()->pdo->commit();
    }
}

class Paginator
{

    const ALIGN_LEFT   = '';
    const ALIGN_CENTER = ' pagination-centered';
    const ALIGN_RIGHT  = ' pagination-right';

    const SIZE_DEFAULT = '';
    const SIZE_LARGE   = ' pagination-large';
    const SIZE_SMALL   = ' pagination-small';
    const SIZE_MINI    = ' pagination-mini';

    /**
     * The results for the current page.
     *
     * @var array
     */
    public $results;

    /**
     * The current page.
     *
     * @var int
     */
    public $page;

    /**
     * The last page available for the result set.
     *
     * @var int
     */
    public $last;

    /**
     * The total number of results.
     *
     * @var int
     */
    public $total;

    /**
     * The number of items per page.
     *
     * @var int
     */
    public $per_page;

    /**
     * The values that should be appended to the end of the link query strings.
     *
     * @var array
     */
    protected $appends;

    /**
     * The compiled appendage that will be appended to the links.
     *
     * This consists of a sprintf format with a page place-holder and query string.
     *
     * @var string
     */
    protected $appendage;

    /**
     * The language that should be used when creating the pagination links.
     *
     * @var string
     */
    protected $language;

    /**
     * The "dots" element used in the pagination slider.
     *
     * @var string
     */
    protected $dots = '<li class="dots disabled"><a href="#">...</a></li>';

    /**
     * Create a new Paginator instance.
     *
     * @param  array  $results
     * @param  int    $page
     * @param  int    $total
     * @param  int    $per_page
     * @param  int    $last
     * @return void
     */
    protected function __construct($results, $page, $total, $per_page, $last)
    {
        $this->page = $page;
        $this->last = $last;
        $this->total = $total;
        $this->results = $results;
        $this->per_page = $per_page;
    }

    /**
     * Create a new Paginator instance.
     *
     * @param  array      $results
     * @param  int        $total
     * @param  int        $per_page
     * @return Paginator
     */
    public static function make($results, $total, $per_page)
    {
        $page = self::page($total, $per_page);

        $last = ceil($total / $per_page);

        return new Paginator($results, $page, $total, $per_page, $last);
    }

    /**
     * Get the current page from the request query string.
     *
     * @param  int  $total
     * @param  int  $per_page
     * @return int
     */
    public static function page($total, $per_page)
    {
        $page = array_get(array_merge($_GET, $_POST), 'page', 1);

        // The page will be validated and adjusted if it is less than one or greater
        // than the last page. For example, if the current page is not an integer or
        // less than one, one will be returned. If the current page is greater than
        // the last page, the last page will be returned.
        if (is_numeric($page) and $page > $last = ceil($total / $per_page)) {
            return ($last > 0) ? $last : 1;
        }

        return (self::valid($page)) ? $page : 1;
    }

    /**
     * Determine if a given page number is a valid page.
     *
     * A valid page must be greater than or equal to one and a valid integer.
     *
     * @param  int   $page
     * @return bool
     */
    protected static function valid($page)
    {
        return $page >= 1 and filter_var($page, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Create the HTML pagination links.
     *
     * Typically, an intelligent, "sliding" window of links will be rendered based
     * on the total number of pages, the current page, and the number of adjacent
     * pages that should rendered. This creates a beautiful paginator similar to
     * that of Google's.
     *
     * Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
     *
     * If you wish to render only certain elements of the pagination control,
     * explore some of the other public methods available on the instance.
     *
     * <code>
     *      // Render the pagination links
     *      echo $paginator->links();
     *
     *      // Render the pagination links using a given window size
     *      echo $paginator->links(5);
     * </code>
     *
     * @param  int     $adjacent
     * @return string
     */
    public function links($adjacent = 3, $alignment = self::ALIGN_LEFT, $size = self::SIZE_DEFAULT)
    {
        //if ($this->last <= 1) return '';

        // The hard-coded seven is to account for all of the constant elements in a
        // sliding range, such as the current page, the two ellipses, and the two
        // beginning and ending pages.
        //
        // If there are not enough pages to make the creation of a slider possible
        // based on the adjacent pages, we will simply display all of the pages.
        // Otherwise, we will create a "truncating" sliding window.
        if ($this->last < 7 + ($adjacent * 2)) {
            $links = $this->range(1, $this->last);
        } else {
            $links = $this->slider($adjacent);
        }

        if ($this->last > 1) $links = $this->previous() . $links . $this->next();

        $content = '<ul class="pagination">' . $links . '</ul>';

        $attributes = array("class" => "pagination-wrapper" . $alignment . $size);

        return '<div' . attributes($attributes) . '>' . $content . '</div>';
    }

    /**
     * Build sliding list of HTML numeric page links.
     *
     * This method is very similar to the "links" method, only it does not
     * render the "first" and "last" pagination links, but only the pages.
     *
     * <code>
     *      // Render the pagination slider
     *      echo $paginator->slider();
     *
     *      // Render the pagination slider using a given window size
     *      echo $paginator->slider(5);
     * </code>
     *
     * @param  int     $adjacent
     * @return string
     */
    public function slider($adjacent = 3)
    {
        $window = $adjacent * 2;

        // If the current page is so close to the beginning that we do not have
        // room to create a full sliding window, we will only show the first
        // several pages, followed by the ending of the slider.
        //
        // Likewise, if the page is very close to the end, we will create the
        // beginning of the slider, but just show the last several pages at
        // the end of the slider. Otherwise, we'll build the range.
        //
        // Example: 1 [2] 3 4 5 6 ... 23 24
        if ($this->page <= $window) {
            return $this->range(1, $window + 2) . ' ' . $this->ending();
        }
        // Example: 1 2 ... 32 33 34 35 [36] 37
        elseif ($this->page >= $this->last - $window) {
            return $this->beginning() . ' ' . $this->range($this->last - $window - 2, $this->last);
        }

        // Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
        $content = $this->range($this->page - $adjacent, $this->page + $adjacent);

        return $this->beginning() . ' ' . $content . ' ' . $this->ending();
    }

    /**
     * Generate the "previous" HTML link.
     *
     * <code>
     *      // Create the "previous" pagination element
     *      echo $paginator->previous();
     *
     *      // Create the "previous" pagination element with custom text
     *      echo $paginator->previous('Go Back');
     * </code>
     *
     * @param  string  $text
     * @return string
     */
    public function previous($text = '<')
    {
        $disabled = ($this->page <= 1);/*function($page) { return $page <= 1; };*/

        return $disabled ? '' : $this->element(__FUNCTION__, $this->page - 1, $text, $disabled);
    }

    /**
     * Generate the "next" HTML link.
     *
     * <code>
     *      // Create the "next" pagination element
     *      echo $paginator->next();
     *
     *      // Create the "next" pagination element with custom text
     *      echo $paginator->next('Skip Forwards');
     * </code>
     *
     * @param  string  $text
     * @return string
     */
    public function next($text = '>')
    {
        $disabled = ($this->page >= $this->last); //function($page, $last) { return $page >= $last; };

        return $disabled ? '' : $this->element(__FUNCTION__, $this->page + 1, $text, $disabled);
    }

    /**
     * Create a chronological pagination element, such as a "previous" or "next" link.
     *
     * @param  string   $element
     * @param  int      $page
     * @param  string   $text
     * @param  Closure  $disabled
     * @return string
     */
    protected function element($element, $page, $text, $disabled)
    {
        $class = "{$element}_page";

        if (is_null($text)) {
            $text = $element;
        }

        // Each consumer of this method provides a "disabled" Closure which can
        // be used to determine if the element should be a span element or an
        // actual link. For example, if the current page is the first page,
        // the "first" element should be a span instead of a link.
        if ($disabled) {
            return '<li' . attributes(array('class' => "{$class} disabled")) . '><a href="#">' . $text . '</a></li>';
        } else {
            return $this->link($page, $text, $class);
        }
    }

    /**
     * Build the first two page links for a sliding page range.
     *
     * @return string
     */
    protected function beginning()
    {
        return $this->range(1, 2) . ' ' . $this->dots;
    }

    /**
     * Build the last two page links for a sliding page range.
     *
     * @return string
     */
    protected function ending()
    {
        return $this->dots . ' ' . $this->range($this->last - 1, $this->last);
    }

    /**
     * Build a range of numeric pagination links.
     *
     * For the current page, an HTML span element will be generated instead of a link.
     *
     * @param  int     $start
     * @param  int     $end
     * @return string
     */
    protected function range($start, $end)
    {
        $pages = array();

        // To generate the range of page links, we will iterate through each page
        // and, if the current page matches the page, we will generate a span,
        // otherwise we will generate a link for the page. The span elements
        // will be assigned the "current" CSS class for convenient styling.
        for ($page = $start; $page <= $end; $page++) {
            if ($this->page == $page) {
                $pages[] = '<li class="active"><a href="#">' . $page . '</a></li>';
            } else {
                $pages[] = $this->link($page, $page, null);
            }
        }

        return implode(' ', $pages);
    }

    /**
     * Create a HTML page link.
     *
     * @param  int     $page
     * @param  string  $text
     * @param  string  $class
     * @return string
     */
    protected function link($page, $text, $class)
    {
        $query = '?page=' . $page . $this->appendage($this->appends);

        $baseUrl = explode('?', urlCurrent())[0];

        $anchor = '<a href="' . $baseUrl . $query . '" data-page="' . $page . '">' . $text . '</a>';

        return '<li' . attributes(array('class' => $class)) . '>' . $anchor . '</li>';
    }

    /**
     * Create the "appendage" to be attached to every pagination link.
     *
     * @param  array   $appends
     * @return string
     */
    protected function appendage($appends)
    {
        // The developer may assign an array of values that will be converted to a
        // query string and attached to every pagination link. This allows simple
        // implementation of sorting or other things the developer may need.
        if (!is_null($this->appendage)) return $this->appendage;

        if (!is_array($appends) || count($appends) <= 0) {
            return $this->appendage = '';
        }

        return $this->appendage = '&' . http_build_query($appends);
    }

    /**
     * Set the items that should be appended to the link query strings.
     *
     * @param  array      $values
     * @return Paginator
     */
    public function appends($values)
    {
        $this->appends = $values;
        return $this;
    }

    /**
     * Set the language that should be used when creating the pagination links.
     *
     * @param  string     $language
     * @return Paginator
     */
    public function speaks($language)
    {
        $this->language = $language;
        return $this;
    }
}

class DB extends Database
{
    public static function close()
    {
        try {
            self::connection()->pdo = null;
        } catch (\Exception $e) {
            //...
        }
    }
}
