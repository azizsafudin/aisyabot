<?php

namespace App\Http\Controllers;
use Mpociot\BotMan\Middleware\Wit;

use App\Conversations\Introduction;
use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;
use GuzzleHttp\Client;
use Carbon\Carbon;

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
    public function getPsiApi(){
        $today = Carbon::createFromFormat('Y-m-d', Carbon::today(), 'Asia/Singapore');
        $client = new Client();
        $uri = 'https://api.data.gov.sg/v1/environment/psi';
        $response = $client->request('GET', $uri, [
            'query' => ['date' => $today],
            'headers' => ['api-key' => env('DATA_GOV_API_KEY')]
        ]);
        $results = json_decode($response->getBody()->getContents(), true);
        $latestpsi = $results['items'][20]['readings']['psi_twenty_four_hourly']['national'];
        return $latestpsi;
    }
}
