<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;
use GuzzleHttp\Client;

class BotCommands extends Controller
{
    public function getPsi($bot){
        $latestpsi = $this->getPsiApi();
        $bot->reply('The latest 24 hourly PSI is '.$latestpsi.'.');
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
