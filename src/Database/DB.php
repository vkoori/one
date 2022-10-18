<?php

namespace One\Database;

class DB
{
    /**
     * @var array
     */
    private static $logs = [];

    /**
     * @var bool
     */
    private static $LoggingStatus = false;

    public static function enableQueryLog(): void
    {
        self::$LoggingStatus = true;
    }

    public static function getQueryLog(): array
    {
        return self::$logs;
    }

    protected static function setQueryLog(string $sql, array $data): void
    {
        if ( self::$LoggingStatus ) {
            array_push(self::$logs, [
                'sql' => $sql,
                'data' => $data
            ]);
        }
    }

    public static function disableQueryLog(): void
    {
        self::$LoggingStatus = false;
    }

}