<?php

namespace panix\mod\telegram;

use Yii;
use yii\base\UserException;
use yii\helpers\Url;
use panix\engine\WebModule;

/**
 * telegram module definition class
 */
class Module extends WebModule implements \yii\base\BootstrapInterface
{
    public $api_token = null;
    public $bot_name = null;
    public $hook_url = null;
    public $password = null;
    public $userCommandsPath = null;
    public $timeBeforeResetChatHandler = 0;
    public $db = 'db';
    public $options = [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'panix\mod\telegram\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('telegram');

        if (isset($config->api_token))
            $this->api_token = $config->api_token;

        if (isset($config->bot_name))
            $this->bot_name = $config->bot_name;

        if (isset($config->password))
            $this->password = $config->password;


        if (empty($this->hook_url))
            throw new UserException('You must set hook_url');
        if (empty($config->password))
            throw new UserException('You must set PASSPHRASE');


        parent::init();

        $this->options = [
            'initChat' => Url::to(['/telegram/default/init-chat']),
            'destroyChat' => Url::to(['/telegram/default/destroy-chat']),
            'getAllMessages' => Url::to(['/telegram/chat/get-all-messages']),
            'getLastMessages' => Url::to(['/telegram/chat/get-last-messages']),
            'initialMessage' => \Yii::t('telegram/default', 'Write your question...'),
        ];

    }

    public function bootstrap($app)
    {
        $config = Yii::$app->settings->get('telegram');
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'panix\mod\telegram\commands';
        }


        $app->setComponents([
            'telegram' => [
                'class' => 'panix\mod\telegram\components\Telegram',
                'botToken' => $config->api_token,
            ]
        ]);

    }


    public function getInfo()
    {
        return [
            'label' => Yii::t('telegram/default', 'MODULE_NAME'),
            'author' => $this->author,
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('telegram/default', 'MODULE_DESC'),
            'url' => ['/admin/telegram'],
        ];
    }

}
