<?php
/*
 * Only allow this script to be run by command line
 */
if(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) {
    header("HTTP/1.0 404 Not Found");
    die();
}

/*
The hostname for your production WordPress installation

Whatever scheme + hostname is in the database exported by mysqldump will be
replaced with the hostname defined here.
*/
$productionSchemeHostname = 'http://WPDeploy.test';

/*
The scheme + hostname for your dev/staging/local WordPress installation
*/
$devSchemeHostname = 'http://testwp.test';

/* Get the command to run */
$command = $argv[1] ?? null;

switch ($command) {
    case 'exportdb':
        WPDeploy::exportDB($productionSchemeHostname, $devSchemeHostname);
        break;
    case 'importdb':
        WPDeploy::importDB();
        break;
    default:
        WPDeploy::missingCommand();
}

function e(string $str)
{
    echo "$str\n";
}

class WPDeploy
{

    # @todo: remove the testwp dir
    private static $wpConfigFile = '../testwp/wp-config.php';

    public static function init()
    {
        if ( ! file_exists(self::$wpConfigFile)) {
            die("Could not load file " . self::$wpConfigFile);
        }
        require_once(self::$wpConfigFile);
    }

    public static function exportDB(string $productionHostname, string $devSchemeHostname)
    {
        self::init();
        $DB_HOST     = DB_HOST;
        $DB_NAME     = DB_NAME;
        $DB_PASSWORD = DB_PASSWORD;
        $DB_USER     = DB_USER;
        exec("MYSQL_PWD=$DB_PASSWORD mysqldump -h $DB_HOST -u $DB_USER $DB_NAME > wp-deploy.sql");
        e("Database exported.");
        exec("sed \"s/".str_replace('/','\/',$devSchemeHostname)."/".str_replace('/','\/',$productionHostname)."/g\" wp-deploy.sql > wp-deploy_prod.sql");
        exec("rm wp-deploy.sql");
        exec("mv wp-deploy_prod.sql wp-deploy.sql");
        e("Replaced [$devSchemeHostname] with [$productionHostname].");
        exec("git add wp-deploy.sql");
        e("Added wp-deploy.sql to git");
    }

    public static function importDB()
    {
        self::init();
    }

    public static function missingCommand()
    {
        e("Missing command. Must be either 'exportdb' or 'importdb'");
    }

}