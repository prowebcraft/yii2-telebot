# yii2-telebot
Telegram Bot extension for Yii2 Framework

# Install
Add dependency to your yii2 project

`composer require prowebcraft/yii2-telebot`

Add migration path to your yii2 config:

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationPath' => null,
        'migrationNamespaces' => [
            'console\migrations', // Common migrations for the your application
            'prowebcraft\yii2telebot\migrations', // Migrations for Yii2 Telebot
        ],
    ],
],
```

Install migrations with `php yii migrate`

This will create tables for storing bot, chat and messages in database.

# Usage

Create Class for your bot, extended from `\prowebcraft\yii2telebot\YiiBot`

Place your bot token in Yii2 params file (for ex. :
```php
<?php
return [
    'bots' => [
        'your_bot_name' => [
            'token' => '111111:AABBCCDDEEFFGG' //place your bot token here
        ]
    ]
];

```

Create basic command:
```php
/**
 * Say hello to you
 */
public function hiCommand()
{
    $this->reply('Hello! ^)');
}
```

Create concole command to run your bot in daemon mode (for ex. `console/controllers/BotController.php`)
```php
<?php
namespace console\controllers;

use common\models\YourBot;

class BotController extends \yii\console\Controller
{

    /**
     * Run bot in daemon mode
     */
    public function actionRun()
    {
        $bot = new YourBot('your_bot_name');
        $bot->start();
    }

}
```

Run your bot with command `php yii bot/run`;

Send `/hi` to your bot in Telegram