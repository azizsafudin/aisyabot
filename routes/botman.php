<?php
use App\Http\Controllers\BotManController;
use Mpociot\BotMan\Middleware\Wit;
use Mpociot\BotMan\BotMan;
use App\Conversations\Introduction;
use Carbon\Carbon;
use App\Conversations\RandomNumberConversation;

// Don't use the Facade in here to support the RTM API too :)
$botman = resolve('botman');
$middleware = Wit::create(env('WIT_AI_ACCESS_TOKEN'));

//This block of commands uses NLP

$botman->hears('salam', function(Botman $bot){
    $bot->reply("Wa'alaikumussalam!");
    $name = $bot->userStorage()->get('name');
    if(is_null($name) || empty($name) || !isset($name)) {
        $bot->startConversation(new Introduction());
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

$botman->hears("my_name_is", function (BotMan $bot) {
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
        $bot->reply("Sorry I didn't catch your name.");
    }

    $bot->reply('I will call you '.$name);
})->middleware($middleware);


//This block of commands are good old fashioned bot commands

$botman->hears("/forgetme@".env('BOT_NAME'), function(Botman $bot){
    $bot->userStorage()->delete();
    $bot->reply("Ok, I've forgotten everything about you.");
});

$botman->hears("/myid@".env('BOT_NAME'), function(Botman $bot){
    $user = $bot->userStorage()->get();
    $userid = $user->get('id');
    if(!isset($userid) || empty($userid) || is_null($userid)) {
        $bot->reply('Your ID has not been set.');
        $bot->replyPrivate('Say: My name is ____.');
    }
    else{
        $bot->reply('Your ID is: '.$userid);
    }
});


$botman->hears('/setcountdown@'.env('BOT_NAME').' {name} {date}', function(Botman $bot, $name, $date){
    $user = $bot->getUser();
    $now = Carbon::createFromFormat('Y-m-d', Carbon::now()->addDays(rand(1,5)));

    if (Carbon::createFromFormat('Y-m-d', $date) == false) {
        $bot->reply('Sorry, but I don\'t understand that format.');
        $bot->typesAndWaits(5);
        $bot->replyPrivate('Try using the format Y-m-d, like so: '.$now);
    }else{
        $bot->userStorage()->save([
            'countdown_name' => $name,
            'countdown_date' => $date,
        ]);
        $bot->reply('I have saved that countdown, ' . $user->getFirstName() . '!');
    }
});

$botman->hears('/countdown@'.env('BOT_NAME'), function(Botman $bot){
    $user    = $bot->userStorage()->get();
    $date    = Carbon::createFromFormat('Y-m-d', $user->get('countdown_date'));
    $diff    = Carbon::now()->diffInDays($date);
    $name    = $user->get('countdown_name');

    if($diff > 0) {
        $bot->reply($name . ' will begin in ' . $diff . ' days!');
    }elseif($diff < 0){
        $bot->reply($name. ' ended '.$diff.' days ago!');
        $bot->userStorage()->delete('countdown_name');
        $bot->userStorage()->delete('countdown_date');
        $bot->typesAndWaits(5);
        $bot->replyPrivate('I have removed that event from my memory.');
    }elseif($diff == 0){
        $bot->reply($name. ' is today!');
        $bot->userStorage()->delete('countdown_name');
        $bot->userStorage()->delete('countdown_date');
        $bot->typesAndWaits(5);
        $bot->replyPrivate('I have removed that event from my memory.');
    }
});

$botman->hears('/removecountdown@'.env('BOT_NAME'), function(Botman $bot){
        $bot->userStorage()->delete('countdown_name');
        $bot->userStorage()->delete('countdown_date');
        $bot->reply('Done!');
});

//for now start_conversation and set_intro does the same thing. Change in the future.
$botman->hears('start_conversation', BotManController::class.'@introConversation')->middleware($middleware);

$botman->hears('set_intro', BotManController::class.'@introConversation')->middleware($middleware);

$botman->fallback(function($bot) {
    $bot->reply("Sorry, I don't quite understand...");
});