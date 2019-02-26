<?php

namespace ChurchCRM\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ChurchCRM\dto\SystemConfig;
use ChurchCRM\dto\SystemURLs;

class LoggerUtils
{
    private static $appLogger;
    private static $appLogHandler;
    private static $cspLogger; 
    private static $chatbotLogger;
    public static function getLogLevel()
    {
        return intval(SystemConfig::getValue("sLogLevel"));
    }

    public static function buildLogFilePath($type)
    {
        return $logFilePrefix = SystemURLs::getDocumentRoot() . '/logs/' . date("Y-m-d") . '-' . $type . '.log';
    }

    /**
     * @return Logger
     */
    public static function getAppLogger($level=null)
    {
      if (is_null(self::$appLogger)){
        // if $level is null 
        // (meaning this function was invoked without explicitly setting the level),
        //  then get the level from the database
        if (is_null($level)) {
          $level = self::getLogLevel();
        }
        self::$appLogger = new Logger('defaultLogger');
        //hold a reference to the handler object so that ResetAppLoggerLevel can be called later on
        self::$appLogHandler = new StreamHandler(self::buildLogFilePath("app"), $level);
        self::$appLogger->pushHandler(self::$appLogHandler);
      }
      return self::$appLogger;
    }
    
    public static function ResetAppLoggerLevel() {
      // if the app log hander was initialized (in the boostrapper) to a specific level
      // before the database initialization occurred
      // we provide a function to reset the app logger to what's defined in the databse.
      self::$appLogHandler->setLevel(self::getLogLevel());
    }

    public static function getCSPLogger()
    {
      if (is_null(self::$cspLogger)){
        self::$cspLogger = new Logger('cspLogger');
        self::$cspLogger->pushHandler(new StreamHandler(self::buildLogFilePath("csp"), self::getLogLevel()));
      }
      return self::$cspLogger;
    }

    public static function getChatBotLogger()
    {
      if (is_null(self::$cspLogger)){
        self::$chatbotLogger = new Logger('chatbot');
        self::$chatbotLogger->pushHandler(new StreamHandler(self::buildLogFilePath("chatbot"), self::getLogLevel()));
      }
      return self::$chatbotLogger;
    }

}