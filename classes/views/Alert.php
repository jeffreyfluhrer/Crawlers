<?php

class Alert
{
    const INFO = 0;
    const SUCCESS = 1;
    const WARNING = 2;
    const ERROR = 3;

    public static function tryRender($alertType=Alert::INFO, $messages)
    {
        if ($messages)
        {
            Alert::render($alertType, $messages);
        }
        else
        {
            echo '';
        }
    }

    public static function render($alertType=Alert::INFO, $messages)
    {
        if (is_array($messages))
        {
            $messages = join($messages, '</br>');
        }
        else
        {
            $messages = $messages;
        }
        
        switch ($alertType)
        {
            case Alert::INFO:
                $alertClass = 'alert-info';
                break;

            case Alert::SUCCESS:
                $alertClass = 'alert-success';
                break;
            
            case Alert::WARNING:
                $alertClass = 'alert-warning';
                break;

            case Alert::ERROR:
                $alertClass = 'alert-danger';
                break;
        }

        echo '<div class="alert ' . $alertClass . ' " role="alert">' . $messages . '</div>';
    }
}