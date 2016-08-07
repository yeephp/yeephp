<?php
/**
 * FileLogger Log File Writer
 *
 * Use this custom log writer to output log messages
 * to a daily, weekly, monthly, or yearly log file. Log
 * files will inherently rotate based on the specified
 * log file name format and the current time.
 *
 * USAGE
 *
 * $app = new \Yee\Yee(array(
 *     'log.writer' => new \Yee\Log\FileLogger()
 * ));
 *
 * SETTINGS
 *
 * You may customize this log writer by passing an array of
 * settings into the class constructor. Available options
 * are shown above the constructor method below.
 */

namespace Yee\Log;

class FileLogger
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * Prepare this log writer. Available settings are:
     *
     * path:
     * (string) The relative or absolute filesystem path to a writable directory.
     *
     * name_format:
     * (string) The log file name format; parsed with `date()`.
     *
     * extension:
     * (string) The file extention to append to the filename`.     
     *
     * message_format:
     * (string) The log message format; available tokens are...
     *     %label%      Replaced with the log message level (e.g. FATAL, ERROR, WARN).
     *     %date%       Replaced with a ISO8601 date string for current timezone.
     *     %message%    Replaced with the log message, coerced to a string.
     *
     * @param   array $settings
     * @return  void
     */
    public function __construct($settings = array())
    {
        //Merge user settings
        $this->settings = array_merge(array(
            'path' => './logs',
            'name_format' => 'Y-m-d',
            'extension' => 'log',
            'message_format' => '%label% - %date% - %message%'
        ), $settings);

      
        //Remove trailing slash from log path
        $this->settings['path'] = rtrim($this->settings['path'], DIRECTORY_SEPARATOR);
    }

    /**
     * Write to log
     *
     * @param   mixed $object
     * @param   int   $level
     * @return  void
     */
    public function write($object, $level)
    {
        //Determine label
        $label = 'DEBUG';
        switch ($level) {
            case \Yee\Log::FATAL:
                $label = 'FATAL';
                break;
            case \Yee\Log::ERROR:
                $label = 'ERROR';
                break;
            case \Yee\Log::WARN:
                $label = 'WARN';
                break;
            case \Yee\Log::INFO:
                $label = 'INFO';
                break;
        }

        //Get formatted log message
        $message = str_replace(array(
            '%label%',
            '%date%',
            '%message%'
        ), array(
            $label,
            date('Y-m-d H:i:s'),
            (string)$object
        ), $this->settings['message_format']);

        //Open resource handle to log file
        if (!$this->resource) {
            $filename = date($this->settings['name_format']);
            if (! empty($this->settings['extension'])) {
                $filename .= '.' . $this->settings['extension'];
            }
            $this->resource = fopen($this->settings['path'] . DIRECTORY_SEPARATOR . $filename, 'a');
        }

       
        
        //Output to resource
        fwrite($this->resource, $message . PHP_EOL);
    }
}
