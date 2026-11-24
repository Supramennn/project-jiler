<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    //    /**
    //     * Sample database connection for SQLite3.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'database'    => 'database.db',
    //        'DBDriver'    => 'SQLite3',
    //        'DBPrefix'    => '',
    //        'DBDebug'     => true,
    //        'swapPre'     => '',
    //        'failover'    => [],
    //        'foreignKeys' => true,
    //        'busyTimeout' => 1000,
    //        'synchronous' => null,
    //        'dateFormat'  => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for Postgre.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'public',
    //        'DBDriver'   => 'Postgre',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'port'       => 5432,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for SQLSRV.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'dbo',
    //        'DBDriver'   => 'SQLSRV',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'encrypt'    => false,
    //        'failover'   => [],
    //        'port'       => 1433,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for OCI8.
    //     *
    //     * You may need the following environment variables:
    //     *   NLS_LANG                = 'AMERICAN_AMERICA.UTF8'
    //     *   NLS_DATE_FORMAT         = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_FORMAT    = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => 'localhost:1521/FREEPDB1',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'DBDriver'   => 'OCI8',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'AL32UTF8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => true,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->applyEnvironmentOverrides();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }

    /**
     * Supports Neon/PostgreSQL without dotted environment variable names,
     * which are awkward to set in Vercel.
     */
    private function applyEnvironmentOverrides(): void
    {
        $host = strtolower(trim((string) ($_SERVER['HTTP_HOST'] ?? '')));
        $isLocalHost = $host === 'localhost' || str_starts_with($host, 'localhost:')
            || $host === '127.0.0.1' || str_starts_with($host, '127.0.0.1:')
            || $host === '::1';
        $environment = strtolower(trim((string) env('CI_ENVIRONMENT', 'production')));

        if ($isLocalHost || $environment === 'development') {
            $this->default = [
                'DSN'          => '',
                'hostname'     => 'localhost',
                'username'     => '',
                'password'     => '',
                'database'     => WRITEPATH . 'database.sqlite',
                'DBDriver'     => 'SQLite3',
                'DBPrefix'     => '',
                'pConnect'     => false,
                'DBDebug'      => true,
                'charset'      => 'utf8',
                'DBCollat'     => '',
                'swapPre'      => '',
                'encrypt'      => false,
                'compress'     => false,
                'strictOn'    => true,
                'failover'    => [],
                'port'        => 0,
                'foreignKeys' => true,
                'busyTimeout' => 1000,
                'synchronous' => null,
                'dateFormat'  => [
                    'date'     => 'Y-m-d',
                    'datetime' => 'Y-m-d H:i:s',
                    'time'     => 'H:i:s',
                ],
            ];

            return;
        }

        $portSet = false;
        $url     = (string) (env('DATABASE_URL_UNPOOLED')
            ?: env('POSTGRES_URL_NON_POOLING')
            ?: env('DATABASE_URL')
            ?: env('POSTGRES_URL')
            ?: '');

        if ($url !== '') {
            $parts = parse_url($url);

            if (is_array($parts)) {
                $host = (string) ($parts['host'] ?? '');

                // CI4 database sessions use PostgreSQL advisory locks.
                // PgBouncer transaction pooling can move those locks to
                // another backend, so prefer Neon's direct endpoint.
                if (str_contains($host, '-pooler.')) {
                    $host = str_replace('-pooler.', '.', $host);
                }

                $this->default['DBDriver'] = 'Postgre';
                $this->default['hostname'] = $host;
                $this->default['port']     = (int) ($parts['port'] ?? 5432);
                $this->default['username'] = urldecode((string) ($parts['user'] ?? ''));
                $this->default['password'] = urldecode((string) ($parts['pass'] ?? ''));
                $this->default['database'] = ltrim((string) ($parts['path'] ?? ''), '/');
                $this->default['charset']  = 'utf8';
                $this->default['DBCollat'] = '';
                $portSet                   = true;

                parse_str((string) ($parts['query'] ?? ''), $query);

                if (isset($query['sslmode'])) {
                    $this->default['sslmode'] = (string) $query['sslmode'];
                }
            }
        }

        $overrides = [
            'DB_DRIVER'   => 'DBDriver',
            'DB_HOST'     => 'hostname',
            'DB_PORT'     => 'port',
            'DB_DATABASE' => 'database',
            'DB_USERNAME' => 'username',
            'DB_PASSWORD' => 'password',
            'DB_PREFIX'   => 'DBPrefix',
            'DB_SSLMODE'  => 'sslmode',
        ];

        foreach ($overrides as $envName => $key) {
            $value = env($envName);

            if ($value === null || $value === false || $value === '') {
                continue;
            }

            if ($key === 'DBDriver') {
                $value = $this->normalizeDriver((string) $value);
            } elseif ($key === 'port') {
                $value = (int) $value;
                $portSet = true;
            }

            $this->default[$key] = $value;
        }

        $debug = env('DB_DEBUG');

        if ($debug !== null && $debug !== false && $debug !== '') {
            $this->default['DBDebug'] = filter_var($debug, FILTER_VALIDATE_BOOLEAN);
        }

        if (($this->default['DBDriver'] ?? '') === 'Postgre') {
            $this->default['charset']  = 'utf8';
            $this->default['DBCollat'] = '';

            if (! $portSet) {
                $currentPort = (int) ($this->default['port'] ?? 0);

                if ($currentPort === 0 || $currentPort === 3306) {
                    $this->default['port'] = 5432;
                }
            }

            $host = (string) ($this->default['hostname'] ?? '');

            if (! isset($this->default['sslmode']) && str_ends_with($host, '.neon.tech')) {
                $this->default['sslmode'] = 'require';
            }
        }
    }

    private function normalizeDriver(string $driver): string
    {
        return match (strtolower($driver)) {
            'pgsql', 'postgres', 'postgresql', 'postgre' => 'Postgre',
            'mysql', 'mysqli'                            => 'MySQLi',
            'sqlite', 'sqlite3'                          => 'SQLite3',
            default                                      => $driver,
        };
    }
}
