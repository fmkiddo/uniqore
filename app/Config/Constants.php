<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);

/**
 | --------------------------------------------------------------------------
 | MySQL datatype constants
 | --------------------------------------------------------------------------
 
 */
defined ('CHAR')                            || define ('CHAR', 'CHAR');
defined ('VARCHAR')                         || define ('VARCHAR', 'VARCHAR');

defined ('TINYTEXT')                        || define ('TINYTEXT', 'TINYTEXT');
defined ('TEXT')                            || define ('TEXT', 'TEXT');
defined ('MEDIUMTEXT')                      || define ('MEDIUMTEXT', 'MEDIUMTEXT');
defined ('LONGTEXT')                        || define ('LONGTEXT', 'LONGTEXT');

defined ('BINARY')                          || define ('BINARY', 'BINARY');
defined ('VARBINARY')                       || define ('VARBINARY', 'VARBINARY');

defined ('TINYBLOB')                        || define ('TINYBLOB', 'TINYBLOB');
defined ('BLOB')                            || define ('BLOB', 'BLOB');
defined ('MEDIUMBLOB')                      || define ('MEDIUMBLOB', 'MEDIUMBLOB');
defined ('LONGBLOB')                        || define ('LONGBLOB', 'LONGBLOB');

defined ('ENUMERATION')                     || define ('ENUMERATION', 'ENUM');
defined ('SET')                             || define ('SET', 'SET');

defined ('TINYINT')                         || define ('TINYINT', 'TINYINT');
defined ('SMALLINT')                        || define ('SMALLINT', 'SMALLINT');
defined ('MEDIUMINT')                       || define ('MEDIUMINT', 'MEDIUMINT');
defined ('INTEGER')                         || define ('INTEGER', 'INT');
defined ('BIGINT')                          || define ('BIGINT', 'BIGINT');

defined ('DECIMAL')                         || define ('DECIMAL', 'DECIMAL');
defined ('FLOATING')                        || define ('FLOATING', 'FLOAT');
defined ('DOUBLE')                          || define ('DOUBLE', 'DOUBLE');
defined ('REAL')                            || define ('REAL', 'REAL');

defined ('BIT')                             || define ('BIT', 'BIT');
defined ('BOOLEAN')                         || define ('BOOLEAN', 'BOOLEAN');
defined ('SERIAL')                          || define ('SERIAL', 'SERIAL');

defined ('DATE')                            || define ('DATE', 'DATE');
defined ('DATETIME')                        || define ('DATETIME', 'DATETIME');
defined ('TIMESTAMP')                       || define ('TIMESTAMP', 'TIMESTAMP');
defined ('TIME')                            || define ('TIME', 'TIME');
defined ('YEAR')                            || define ('YEAR', 'YEAR');

/**
 | --------------------------------------------------------------------------
 | 
 | --------------------------------------------------------------------------
 */
defined ('GOOGLE_RECAPTCHAV3_SITEKEY')      || define ('GOOGLE_RECAPTCHAV3_SITEKEY', '6LetgzQqAAAAAC2pL2jTdajJfI5JEwmL6PENqsAE');
defined ('GOOGLE_RECAPTCHAV3_SECRETKEY ')   || define ('GOOGLE_RECAPTCHAV3_SECRETKEY ', '6LetgzQqAAAAAMU1J98txpddlNq_eOZz6ucm18-M');
defined ('SYS__UNIQORE_RANDAUTH_PATH')      || define ('SYS__UNIQORE_RANDAUTH_PATH', '../.randauth');
defined ('SYS__DATABASE_ROOTC')             || define ('SYS__DATABASE_ROOTC', 
    [
        'DSN'           => '',
        'hostname'      => 'localhost',
        'username'      => 'root',
        'password'      => '6O98TI8m!]psDR62',
        'database'      => 'mysql',
        'DBDriver'      => 'MySQLi',
        'DBPrefix'      => '',
        'pConnect'      => FALSE,
        'DBDebug'       => FALSE,
        'charset'       => 'utf8mb4',
        'DBCollat'      => 'utf8mb4_unicode_520_ci',
        'swapPre'       => '',
        'encrypt'       => FALSE,
        'compress'      => FALSE,
        'strictOn'      => FALSE,
        'failover'      => [],
        'port'          => 3306
    ]);
defined ('HEADER_APP_FORM')                 || define ('HEADER_APP_FORM', 'application/x-www-form-urlencoded');
defined ('HEADER_APP_MULTIPART')            || define ('HEADER_APP_MULTIPART', 'multipart/form-data');
defined ('HEADER_APP_JSON')                 || define ('HEADER_APP_JSON', 'application/json');
defined ('UNIQORE_NAME')                    || define ('UNIQORE_NAME', 'Uniqore');
defined ('UNQIORE_TITLE')                   || define ('UNIQORE_TITLE', 'Uniqore API');
defined ('UNIQORE_RANDOM_PASSCODE')         || define ('UNIQORE_RANDOM_PASSCODE', 32);
defined ('UNIQORE_RANDOM_CLIENT')           || define ('UNIQORE_RANDOM_CLIENT', 5);
defined ('UNQIORE_RANDOM_DBPSWD')           || define ('UNQIORE_RANDOM_DBPSWD', 16);