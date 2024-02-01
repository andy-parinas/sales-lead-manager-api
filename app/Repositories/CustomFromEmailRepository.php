<?php


namespace App\Repositories;


use App\Repositories\Interfaces\CustomFromEmailInterface;

class CustomFromEmailRepository implements CustomFromEmailInterface
{
    public function emailFrom($username)
    {
        $fromEmail = [
            'headoffice1' => 'spanline@spanline.com.au',
            'headoffice2' => 'spanline@spanline.com.au',
            'headoffice3' => 'spanline@spanline.com.au',            
            'KarenBoweFA-2640MA' => 'albury@spanline.com.au',
            'NickMaddenFA-2527' => 'illawarra@spanline.com.au',
            'SylviaMaddenFA-2527' => 'illawarra@spanline.com.au',            
            'SonjaMilevskiFA-2527' => 'illawarra@spanline.com.au',
            'DaveRichenFA-2527' =>	'illawarra@spanline.com.au',
            'VeronicaElasiFA-2527' => 'illawarra@spanline.com.au',
            'JasonWearFA-2450SH' =>	'northcoast@spanline.com.au',
            'TristanWaddellFA-2450SH' => 'northcoast@spanline.com.au',
            'MelWilliamsFA-2450SH' => 'northcoast@spanline.com.au',
            'ChrisGorstFA-3555' =>	'Bendigo@spanline.com.au',
            'JonathanGorstFA-3555' => 'Bendigo@spanline.com.au',
            'RhondaOwenFA-3555' => 'Bendigo@spanline.com.au',
            'AshleaPotterFA-3555' => 'Bendigo@spanline.com.au',
            'ChrisHoltFA-4740' => 'mackay@spanline.com.au',
            'TaniaBattsonFA-4740' => 'mackay@spanline.com.au',
            'CarmelHoltFA-4740' => 'mackay@spanline.com.au',
            'CaitlinCoombeFA-2285' => 'newcastle@spanline.com.au',
            'TonyCommissoFA-2650CG' => 'riverina@spanline.com.au',
            'SebastianCommissoFA-2650CG' => 'riverina@spanline.com.au',
            'KarineMoraoFA-2650CG' => 'riverina@spanline.com.au',
            'CarolinaSerrazesFA-2650CG' => 'riverina@spanline.com.au',
            'MandiGeeSU-2911CG' => 'act@spanline.com.au',
            'SofiaOsmanFA-2911CG' => 'act@spanline.com.au',
            'HelenMurraySU-2650CG' => 'iverina@spanline.com.au',
            'VanessaBlandSU-2650CG' => 'riverina@spanline.com.au',
            'SelinaFoenanderFA-4127' =>	'brissouth@spanline.com.au',
            'LesleySonterFA-4127' => 'brissouth@spanline.com.au',
            'DavidLindsayFA-4127' => 'brissouth@spanline.com.au',
            'JohnSandoFA-4074' => 'briswest@spanline.com.au',
            'SimoneAurinoFA-2315' => 'portstephens@spanline.com.au',
            'KylieDuffyFA-2315' => 'portstephens@spanline.com.au',
            'HeatherMicallefFA-2444PH' => 'midcoast@spanline.com.au',
            'TiffanyTaberFA-2800' => 'orange@spanline.com.au',
            'DorothyJonesFA-2800' => 'orange@spanline.com.au',
            'JoelFahyFA-2800' => 'orange@spanline.com.au',
            'GregBoufflerFA-2800' => 'orange@spanline.com.au',
            'SuzanneClowFA-2830' =>	'dubbo@spanline.com.au',
            'CarynDavisFA-2830' => 'dubbo@spanline.com.au',
            'RosanneBerryFA-2830' => 'dubbo@spanline.com.au',
            'GlennEganSU-2340' => 'tamworth@spanline.com.au',
            'SamEganSU-2340' =>	'tamworth@spanline.com.au',
        ];
        
        if(array_key_exists($username, $fromEmail)){
            return $fromEmail[$username];
        }else{
            return 'support@spanline.com.au';
        }
    }
}
