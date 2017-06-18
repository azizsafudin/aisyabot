<?php

namespace App\Http\Controllers;
use Mpociot\BotMan\Middleware\Wit;

use App\Conversations\ExampleConversation;
use App\Conversations\Introduction;
use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;

class BotManController extends Controller
{
	/**
	 * Place your BotMan logic here.
	 */
    public function handle()
    {
    	$botman = app('botman');

    	//Uncomment to set all hears to wit.ai middleware
    	//$botman->middleware(Wit::create(env('WIT_AI_ACCESS_TOKEN')));

        $middleware = Wit::create(env('WIT_AI_ACCESS_TOKEN'));

        $botman->listen();
        return response()->json(['message' =>'success']);
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function introConversation(BotMan $bot)
    {
        $bot->startConversation(new Introduction());
    }
}
