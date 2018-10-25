<?php

namespace App\DataManager\Mappings;

use App\Models\Anime;
use App\Models\AnimeName;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class KitsuMapper
{
    public static function getKitsuId($malid) {
        $re = curl_init("https://kitsu.io/api/edge/mappings?filter[external_site]=myanimelist/anime&filter[external_id]={$malid}&include=item");

        curl_setopt_array($re, array(
            CURLOPT_POST => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/vnd.api+json'
            )
        ));

        // Send the request
        $response = curl_exec($re);

        // Check for errors
        if($response === FALSE){
            return null;
        }

        // Decode the response
        $responseData = json_decode($response, TRUE);

        if (count($responseData["data"]) > 0) {
            return $responseData["data"][0]["relationships"]["item"]["data"]["id"];
        } else {
            return null;
        }
    }

    public static function FillDatabase()
    {
        $animes = Anime::all();
        foreach ($animes as $anime) {
            if ($anime->kitsu_id === null && $anime->mal_id !== null) {
                $anime->kitsu_id = self::getKitsuId($anime->mal_id);
                $anime->save();
                Log::info("kitsu-addid", $anime->toArray());
                sleep(1); // Avoid Too many Requests
            }
        }
    }
}
