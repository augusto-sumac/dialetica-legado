<?php

class InsertOrUpdateMany
{
    public static function dbQuote($value)
    {
        return DB::escape($value);
    }

    public static function getCols($cols)
    {
        return '`' . implode('`,`', $cols) . '`';
    }

    public static function getRow($row, $cols)
    {
        return "(" . implode(',', array_map(function ($col) use ($row) {
            $value = array_get((array) $row, $col, null);
            return $value !== null ? static::dbQuote($value) : 'NULL';
        }, $cols)) . ")";
    }

    public static function getOnDuplicateCols($onDuplicateCols)
    {
        return implode(',', array_map(function ($col) {
            // return "`$col`=VALUES(`$col`)";
            if (in_array($col, ['first_sale'])) {
                return "`$col` = LEAST(COALESCE( VALUES(`$col`), `$col`), `$col`)";
            }

            if (in_array($col, ['last_sale'])) {
                return "`$col` = GREATEST(COALESCE( VALUES(`$col`), `$col`), `$col`)";
            }

            return "`$col` = VALUES(`$col`)";
        }, $onDuplicateCols));
    }

    public static $insertOrUpdateSqlTmpl =
    'INSERT INTO `%s` 
(%s) 
VALUES 
%s 
ON DUPLICATE KEY UPDATE 
%s';

    public static $queries = [];

    public static function prepare(
        string $table,
        array $cols,
        array $insertOrUpdateRows,
        array $onDuplicateCols,
        callable $getIdValue = null,
        bool $runAfterPrepare = false
    ) {
        static::$queries = [];

        $insertOrUpdateRows = array_values((array) $insertOrUpdateRows);

        foreach (array_chunk($insertOrUpdateRows, 200) as $rows) {
            $values = implode(',', array_map(function ($row) use ($cols, $getIdValue) {
                if ($getIdValue) {
                    $row = $getIdValue($row);
                }

                return static::getRow($row, $cols);
            }, $rows));

            $sql = sprintf(
                static::$insertOrUpdateSqlTmpl,
                $table,
                static::getCols($cols),
                $values,
                static::getOnDuplicateCols($onDuplicateCols)
            );

            static::$queries[] = $sql;
        }

        if ($runAfterPrepare) {
            static::run();
        }
    }

    public static function run()
    {
        foreach (static::$queries as $sql) {
            DB::query($sql);
        }
    }
}
