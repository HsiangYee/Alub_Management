<?php

namespace App\Http\Controllers;

use App\Account;
use App\Album;
use App\Image;

use Illuminate\Http\Request;
use \SimpleXMLElement;

class AccountController extends Controller{

    public function create(Request $request){
        $xml = simplexml_load_string($request->getContent());

        $CheckAccount = Account::where('Account', $xml->account) -> count();
        $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';

        if($CheckAccount > 0){
            $token = "";
            $response_xml = $response_xml.'<data type="string" success="0" status="400">此帳號已經被註冊</data>';
        }else{
            do{
                $token = "";
                for($i = 0 ; $i < 7 ; $i ++){
                    $rand = rand(0, 2);
                    if($rand == 0){
                        $chr = chr(rand(97, 122));
                        $token = $token.$chr;
                    }
                    if($rand == 1){
                        $chr = chr(rand(65, 90));
                        $token = $token.$chr;
                    }
                    if($rand == 0){
                        $token = $token.rand(0, 9);
                    }
                }
    
                $CheckToken = Account::where('Token', $token) -> count();
            }while($CheckToken > 0);
    
            $Account = new Account;
            $Account->Token = $token;
            $Account->Account = $xml->account;
            $Account->Bio = $xml->bio;
            $Account->save();

            $response_xml = $response_xml.'<data type="string" success="1" status="200">'.$token.'</data>';
        }
        
        return response($response_xml)
            ->header('Content-Type', 'application/xml')
            ->header('Authorization', 'Token '.$token);
    }

    public function show(Request $request, $accountID){
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $CheckToken = Account::where('Token', $token) -> first();
        $CheckAccountId = Account::where('Token', $accountID) -> first();
        if(isset($CheckToken) && isset($CheckAccountId)){
            $Account = Account::find($CheckAccountId->id);

            $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $response_xml = $response_xml.'<data success="1" status="200">';
            $response_xml = $response_xml.'<account>'.$Account->Account.'</account>';
            $response_xml = $response_xml.'<bio>'.$Account->Bio.'</bio>';
            $response_xml = $response_xml.'<created>'.strtotime($Account->created_at).'</created><!-- UnixTimestamp -->';
            $response_xml = $response_xml.'<albums>';

            $Albums = Album::where('AccountToken', $Account->Token) -> get();
            foreach($Albums as $Album){
                $image_count = Image::where('AlbumId', $Album->id2) -> count();
                $response_xml = $response_xml.'<album id="'.$Album->id2.'" count="'.$image_count.'"/>';
            }
            $response_xml = $response_xml.'</albums>';
            $response_xml = $response_xml.'</data>';

            return response($response_xml)
                ->header('Content-Type', 'applocation/xml')
                ->header('Authorization', 'Token '.$token);
        }elseif(isset($CheckToken) && !isset($CheckAccountId)){
            $response_xml = '<data success="0" status="404" />';
            return response($response_xml)
                ->header('Content-Type', 'applocation/xml')
                ->header('Authorization', 'Token '.$token);
        }
    }
}