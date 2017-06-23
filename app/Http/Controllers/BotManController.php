<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\Paginator;
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
    	$botman->middleware(Wit::create(env('WIT_AI_ACCESS_TOKEN')));

        $botman->hears('get_prayertimes', function (BotMan $bot) {
            $bot->types();
            $results = $this->getPrayerTimes();
            $bot->reply($results);
        });

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

    public function getPrayerTimes($source = 'muis'){
        if($source == 'muis') {
            $url = "http://http://www.muis.gov.sg/";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $dom = new \DOMDocument();
            $dom->loadHTML(htmlspecialchars($data));

            $prayertimes = [
                $dom->GetElementById('PrayerTimeControl1_lblSubuh')->textContent => $dom->GetElementById('PrayerTimeControl1_Subuh')->textContent,
                $dom->GetElementById('PrayerTimeControl1_lblSyuruk')->textContent => $dom->GetElementById('PrayerTimeControl1_Syuruk')->textContent,
                $dom->GetElementById('PrayerTimeControl1_lblZohor')->textContent => $dom->GetElementById('PrayerTimeControl1_Zohor')->textContent,
                $dom->GetElementById('PrayerTimeControl1_lblAsar')->textContent => $dom->GetElementById('PrayerTimeControl1_Asar')->textContent,
                $dom->GetElementById('PrayerTimeControl1_lblMaghrib')->textContent => $dom->GetElementById('PrayerTimeControl1_Maghrib')->textContent,
                $dom->GetElementById('PrayerTimeControl1_lblIsyak')->textContent =>    $dom->GetElementById('PrayerTimeControl1_Isyak')->textContent,
            ];
            $message =  'The prayer times for today, according to MUIS are:\n';
            foreach ($prayertimes as $k => $v){
                $message .= $k. ' - '.$v.'\n';
            }
            return $message;
        }
        return 'Sorry, I was unable to read prayertime data.';
    }

//    public function getPsiApi(){
//        $today = Carbon::createFromFormat('Y-m-d', Carbon::today(), 'Asia/Singapore');
//        $client = new Client();
//        $uri = 'https://api.data.gov.sg/v1/environment/psi';
//        $response = $client->request('GET', $uri, [
//            'query' => ['date' => $today],
//            'headers' => ['api-key' => env('DATA_GOV_API_KEY')]
//        ]);
//        $results = json_decode($response->getBody()->getContents(), true);
//        $latestpsi = $results['items'][20]['readings']['psi_twenty_four_hourly']['national'];
//        return $latestpsi;
//    }
}
