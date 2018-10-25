<?php

namespace App\DataManager\Mappings;

use App\Models\Anime;
use App\Models\AnimeName;
use Illuminate\Support\Facades\Log;

class AnilistMapper
{
    public static function getAnilistId($malid) {
        $postData = array(
            'query' => 'query($malId: Int) { Media (idMal: $malId, type: ANIME) { id title { romaji native english } } }',
            'variables' => array('malId' => $malid)
        );

        $re = curl_init('https://graphql.anilist.co');

        curl_setopt_array($re, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        // Send the request
        $response = curl_exec($re);

        // Check for errors
        if($response === FALSE){
            return null;
        }

        // Decode the response
        $responseData = json_decode($response, TRUE);

        return $responseData["data"]["Media"];
    }

    public static function FillDatabase()
    {
        $animes = Anime::all();
        foreach ($animes as $anime) {
            if ($anime->anilist_id === null && $anime->mal_id !== null) {
                $anilist = self::getAnilistId($anime->mal_id);
                $anime->anilist_id = $anilist["id"];
                $anime->save();
                Log::info("anilist-addid", $anime->toArray());
                // Add Titles
                if ($anilist["title"]["romaji"] !== null) {
                    AnimeName::create(array(
                        'anime_id' => $anime->id,
                        'title' => $anilist["title"]["romaji"],
                        'language' => 'ja-ro'
                    ));
                }

                if ($anilist["title"]["native"] !== null) {
                    AnimeName::create(array(
                        'anime_id' => $anime->id,
                        'title' => $anilist["title"]["native"],
                        'language' => 'na-NA'
                    ));
                }

                if ($anilist["title"]["english"] !== null) {
                    AnimeName::create(array(
                        'anime_id' => $anime->id,
                        'title' => $anilist["title"]["english"],
                        'language' => 'en-US'
                    ));
                }
                sleep(1); // Avoid Too many Requests
            }
        }
    }
}
