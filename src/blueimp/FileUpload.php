<?php

namespace Alverated\JQueryFileUpload;

require_once __DIR__.'/../../../../blueimp/jquery-file-upload/server/php/UploadHandler.php';

class FileUpload extends \UploadHandler{

    protected $options;

    public function __construct($options = null, $initialize = true, $error_messages = null)
    {
        $this->options = [
            'csrf_token' => [
                'name'  => '_token',
                'value' => csrf_token()
            ],
            'script_url' => \URL::current(),
            'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/files/',
            'upload_url' => $this->get_full_url().'/files/',
        ];

        if ($options) {
            $this->options = $options + $this->options;
        }

        parent::__construct($this->options, $initialize, $error_messages);
    }

    protected function set_additional_file_properties($file)
    {
        $file->deleteUrl = $this->options['script_url']
            .$this->get_query_separator($this->options['script_url'])
            .$this->get_singular_param_name()
            .'='.rawurlencode($file->name);

        if($this->options['csrf_token'])
        {
            $file->deleteUrl .= '&';
            if(isset($this->options['csrf_token']['name']))
                $file->deleteUrl .= $this->options['csrf_token']['name'];

            if(isset($this->options['csrf_token']['value']))
                $file->deleteUrl .= '='.$this->options['csrf_token']['value'];
            else
                $file->deleteUrl .= '='.csrf_token();
        }

        $file->deleteType = $this->options['delete_type'];
        if ($file->deleteType !== 'DELETE') {
            $file->deleteUrl .= '&_method=DELETE';
        }
        if ($this->options['access_control_allow_credentials']) {
            $file->deleteWithCredentials = true;
        }
    }

    public function current_url()
    {
        $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

        if ($_SERVER["SERVER_PORT"] != "80")
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        return $pageURL;
    }
}