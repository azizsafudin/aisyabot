<?php
use App\Http\Controllers\BotManController;
use Mpociot\BotMan\Middleware\Wit;
use Mpociot\BotMan\BotMan;
use App\Conversations\Introduction;
use Carbon\Carbon;
use App\Conversations\RandomNumberConversation;
use App\Bot;

// Don't use the Facade in here to support the RTM API too :)
$botman = resolve('botman');
$middleware = Wit::create(env('WIT_AI_ACCESS_TOKEN'));

//This block of commands uses NLP

$botman->hears('salam', function(Botman $bot){
    $bot->reply('Wa\'alaikumussalam!');
    $name = $bot->userStorage()->get('name');
    if(is_null($name) || empty($name) || !isset($name)) {
        $bot->reply('I don\'t think we\'ve met...');
        $bot->typesAndWaits(5);
        $bot->reply('What is your name?');
    }
})->middleware($middleware);

$botman->hears('get_random_number', function(Botman $bot){
    $bot->startConversation(new RandomNumberConversation());
})->middleware($middleware);

$botman->hears('who_am_i', function(Botman $bot){
    $user = $bot->userStorage()->get();
    if ($user->has('name')) {
        $bot->reply('Your name is '.$user->get('name'));
    } else {
        $bot->reply('I do not know you yet.');
        $bot->startConversation(new Introduction());
    }
})->middleware($middleware);

$botman->hears('set_name', function (BotMan $bot) {
    $bot->userStorage()->delete('name');

    $extras   = $bot->getMessage()->getExtras();
    $entities = $extras['entities'];
    try {
        $name = $entities['contact'][0]['value'];
        $bot->userStorage()->save([
            'name' => $name,
            'id'   => $bot->getUser()->getId(),
        ]);
    }
    catch(\Exception $e){
        $bot->reply('Sorry I didn\'t catch your name.');
    }

    $bot->reply('Nice to meet you, '.$name.'!');
})->middleware($middleware);

$botman->hears('get_bot_info', function(BotMan $bot){
        $bot->reply('My name is '.env('BOT_NAME'));
        $bot->typesAndWaits(3);
        $bot->reply('I was created by Abdul Aziz.');
        $bot->reply('Also known as, @modulus');
});
//This block of commands are good old fashioned bot commands

$botman->hears('forget_me', function(Botman $bot){
    $bot->userStorage()->delete();
    $bot->reply('Ok, I\'ve forgotten everything about you.');
});

//for now start_conversation and set_intro does the same thing. Change in the future.
//$botman->hears('start_conversation', BotManController::class.'@introConversation')->middleware($middleware);

//$botman->hears('set_intro', BotManController::class.'@introConversation')->middleware($middleware);

$botman->fallback(function($bot) {
    $fallback = Bot::FALLBACK[rand(0,7)];
    $bot->reply($fallback);
});