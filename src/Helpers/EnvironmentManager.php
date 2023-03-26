<?php

namespace Alex\LaravelDocSchema\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $results = trans('installer_messages.environment.success');
        try {
            $envFilePath = $this->envPath;
            $envFileData = file_get_contents($envFilePath);
            $envFileData = str_replace([
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:t2wZGSpe9Jj3py7uFcftBOzrNc+wxnqRhWjqORGWkYI=',
                'APP_DEBUG=false',
                'APP_URL=',
                'DB_CONNECTION=',
                'DB_HOST=',
                'DB_PORT=',
                'DB_DATABASE=',
                'DB_USERNAME=',
                'DB_PASSWORD=',
            ], [
                'APP_NAME='.$request->input('app_name'),
                'APP_ENV='.$request->input('environment'),
                'APP_KEY='.'base64:'.base64_encode(Str::random(32)),
                'APP_DEBUG='.$request->input('app_debug'),
                'APP_URL='.$request->input('app_url'),
                'DB_CONNECTION='.$request->input('database_connection'),
                'DB_HOST='.$request->input('database_hostname'),
                'DB_PORT='.$request->input('database_port'),
                'DB_DATABASE="'.$request->input('database_name').'"',
                'DB_USERNAME="'.$request->input('database_username').'"',
                'DB_PASSWORD="'.$request->input('database_password').'"'
            ], $envFileData);
            file_put_contents($envFilePath, $envFileData);
        } catch (Exception $e) {
            $results = trans('installer_messages.environment.errors');
        }
        return $results;
    } 
}
