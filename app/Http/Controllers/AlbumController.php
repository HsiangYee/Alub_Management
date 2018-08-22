<?php

namespace App\Http\Controllers;

use App\Account;
use App\Album;
use App\Image;

use Illuminate\Http\Request;
use Response;

class AlbumController extends Controller{

    public function create(Request $request){
        $xml = simplexml_load_string($request->getContent());
        $token = str_replace('Token ', '', $request->header('Authorization'));

        $CheckToken = Account::where('Token', $token) -> count();
        if($CheckToken > 0){
            do{
                $length = rand(5, 11);
                $id = "";
                for($i = 0 ; $i < $length ; $i ++){
                    $rand = rand(0, 2);
                    if($rand == 0){
                        $chr = chr(rand(97, 122));
                        $id = $id.$chr;
                    }
                    if($rand == 1){
                        $chr = chr(rand(65, 90));
                        $id = $id.$chr;
                    }
                    if($rand == 2){
                        $id = $id.rand(0, 9);
                    }
                 }

                $CheckId = Album::where('id2', $id) -> count();
            }while($CheckId > 0);

            $Album = new Album;
            $Album->id2 = $id;
            $Album->AccountToken = $token;
            $Album->Title = $xml->title;
            $Album->Description = $xml->description;
            $Album->save();

            $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $response_xml = $response_xml.'<data type="string" success="1" status="200">'.$id.'</data>';

            return response($response_xml)
                ->header('Content-Type', 'application/xml')
                ->header('Authorization', 'Token '.$token);;
        }
    }

    public function upload(Request $request, $album_id){
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $CheckToken = Account::where('Token', $token) -> first();
        $Album = Album::where('id2', $album_id) -> first();
        if(isset($CheckToken) && isset($Album)){
            do{
                $id = '';
                for($i = 0 ; $i < 10 ; $i ++){
                    $rand = rand(0, 2);
                    if($rand == 0){
                        $chr = chr(rand(97, 122));
                        $id = $id.$chr;
                    }
                    if($rand == 1){
                        $chr = chr(rand(65, 90));
                        $id = $id.$chr;
                    }
                    if($rand == 2){
                        $id = $id.rand(0, 9);
                    }
                }

                $CheckId = Image::where('id2', $id) -> count();
            }while($CheckId > 0);

            $Image = new Image;
            $Image->id2 = $id;
            $Image->AlbumId = $album_id;
            $Image->Cover = 0;
            $Image->Title = $request->title;
            $Image->Description = $request->description;
            $Image->view = 0;
            $Image->DeleteToken = "";
            $Image->save();

            $upload_image = $request->file('image');
            $upload_image_filesize = filesize($upload_image);
            $upload_image_size = getimagesize($upload_image);
            $upload_image_type = str_replace('image/', '', $upload_image_size['mime']);

            switch($upload_image_type){
                case 'jpeg':
                    $image = imagecreatefromjpeg($upload_image);
                break;

                case 'png':
                    $image = imagecreatefrompng($upload_image);
                break;

                case 'gif':
                    $image = imagecreatefromgif($upload_image);
                break;
            }
            
            imagejpeg($image, base_path().'/images/'.$id.'.jpg');
            $img_src = base_path().'/images/'.$id.'.jpg';
            
            function create_thumb($id, $src, $thumb, $size){
                $img_src = imagecreatefromjpeg($src);
                $src_w = imagesx($img_src);
                $src_h = imagesy($img_src);

                if($size != 't'){
                    if($src_w > $src_h){
                        $thumb_w = $thumb;
                        $thumb_h = intval($src_h / $src_w * $thumb);
                    }else{
                        $thumb_h = $thumb;
                        $thumb_w = intval($src_w / $src_h * $thumb);
                    }
                }else{
                    $thumb_h = $thumb;
                    $thumb_w = $thumb;
                }
                $thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);

                imagecopyresampled($thumb_img, $img_src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);

                imagejpeg($thumb_img, base_path().'/images/'.$id.$size.'.jpg');
            }
            create_thumb($id, $img_src, 960, 'l');
            create_thumb($id, $img_src, 320, 'm');
            create_thumb($id, $img_src, 90, 's');
            create_thumb($id, $img_src, 50, 't');

            $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $response_xml = $response_xml.'<data success="1" status="200">';
            $response_xml = $response_xml.'<datetime>'.strtotime($Image->created_at).'</datetime>';
            $response_xml = $response_xml.'<width>'.$upload_image_size[0].'</width>';
            $response_xml = $response_xml.'<height>'.$upload_image_size[1].'</height>';
            $response_xml = $response_xml.'<size>'.$upload_image_filesize.'</size>';
            $response_xml = $response_xml.'</data>';

            return response($response_xml)
                ->header('Content-Type', 'application/xml')
                ->header('Authorization', 'Token '.$token);
        }
    }

    public function update(Request $request, $album_id){
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $CheckToken = Account::where('Token', $token) -> first();
        $Album = Album::where('id2', $album_id) -> first();
        if(isset($CheckToken) && isset($Album) && $token == $Album->AccountToken){
            $xml = simplexml_load_string($request->getContent());

            if($xml->title != ""){
                $Album->Title = $xml->title;
            }

            if($xml->description != ""){
                $Album->Description = $xml->description;
            }

            $Album->save();

            if(count($xml->covers->cover) > 0){
                for($i = 0 ; $i < count($xml->covers->cover) ; $i ++){
                    $Image = Image::where('id2', $xml->covers->cover[$i]) -> first();
                    if(isset($Image)){
                        $Image->Cover = 1;
                        $Image->save();
                    }
                }
            }
            
            $response_xml = '<?xml version="1.0" encoding="UTF-8"?><data success="1" status="200"/>';

            return response($response_xml)
                ->header('Contetn-Type', 'applocation/xml');
        }
    }


    public function info($album_id){
        $Album = Album::where('id2', $album_id) -> first();
        $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
        if(!isset($Album)){
            $response_xml = $response_xml.'<data success="0" status="404" />';
        }else{  
            $response_xml = $response_xml.'<data success="1" status="200">';
            $response_xml = $response_xml.'<id>'.$album_id.'</id>';
            $response_xml = $response_xml.'<title>'.$Album->Title.'</title>';
            $response_xml = $response_xml.'<description>'.$Album->Description.'</description>';
            $response_xml = $response_xml.'<datetime>'.strtotime($Album->created_at).'</datetime>';
            $cover_count = Image::where('AlbumId', $album_id) -> where('Cover', '1') -> count();
            if($cover_count == 0){
                $response_xml = $response_xml.'<covers/>';
            }else{
                $covers = Image::where('AlbumId', $album_id) -> where('Cover', '1') -> get();
                $response_xml = $response_xml.'<covers>';
                foreach($covers as $cover){
                    $response_xml = $response_xml.'<cover>'.$cover->id2.'</cover>';    
                }
                $response_xml = $response_xml.'</covers>';
            }
            $response_xml = $response_xml.'<account>'.$Album->AccountToken.'</account>';
            $response_xml = $response_xml.'<link>http://127.0.0.1/XX_Module_F/album/'.$album_id.'</link>';

            $image_count = Image::where('AlbumId', $album_id) -> count();
            $response_xml = $response_xml.'<images_count>'.$image_count.'</images_count>';
            if($image_count == 0){
                $response_xml = $response_xml.'<images/>';
            }else{
                $images = Image::where('AlbumId', $album_id) -> get();
                $response_xml = $response_xml.'<images>';
                foreach($images as $image){
                    $response_xml = $response_xml.'<item>';

                    $response_xml = $response_xml.'<id>'.$image->id2.'</id>';
                    $response_xml = $response_xml.'<title>'.$image->Title.'</title>';

                    if($image->Description == ""){
                        $response_xml = $response_xml.'<description/>';
                    }else{
                        $response_xml = $response_xml.'<description>'.$image->Description.'</description>';
                    }

                    $response_xml = $response_xml.'<datetime>'.strtotime($image->created_at).'</datetime>';
                    $response_xml = $response_xml.'<width>'.$image->width.'</width>';
                    $response_xml = $response_xml.'<height>'.$image->height.'</height>';
                    $response_xml = $response_xml.'<size>'.$image->size.'</size>';
                    $response_xml = $response_xml.'<views>'.$image->view.'</views>';
                    $response_xml = $response_xml.'<link>http://127.0.0.1/XX_Module_F/i/'.$image->id2.'.jpg</link>';

                    $response_xml = $response_xml.'</item>';
                }
                $response_xml = $response_xml.'</images>';
            }

            $response_xml = $response_xml.'</data>';
        }

        return response($response_xml)
            ->header('Content-Type', 'application/xml');
    }

    public function image($imageId, $imageSuffix){
        if(strlen($imageId.$imageSuffix) > 10){
            $CheckImage = Image::where('id2', $imageId) -> first();
            if(isset($CheckImage)){
                $CheckImage->view = $CheckImage->view + 1;
                $CheckImage->save();

                $img = file_get_contents(base_path('images/'.$imageId.$imageSuffix.".jpg"));
                return response($img)
                ->header('Content-Type', 'image/jpg');
            }
            
        }else{
            $CheckImage = Image::where('id2', $imageId.$imageSuffix) -> first();
            if(isset($CheckImage)){
                $CheckImage->view = $CheckImage->view + 1;
                $CheckImage->save();

                $img = file_get_contents(base_path('images/'.$imageId.$imageSuffix.".jpg"));
                return response($img)
                ->header('Content-Type', 'image/jpg');
            }
        }
    }

    public function move(Request $request){
        $xml = simplexml_load_string($request->getContent());
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $Token = Account::where('Token', $token) -> first();
        $Image = Image::where('id2', $xml->src_image) -> first();
        $Album = Album::where('id2', $xml->dst_album) -> first();
        if(isset($Token) && isset($Image) && isset($Album)){
            $Image->AlbumId = $xml->dst_album;
            $Image->save();

            $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $response_xml = $response_xml.'<data success="1" status="204"/>';

            return response($response_xml)
                ->header('Contetn-Type', 'application/xml')
                ->header('Authorization', 'Token '.$token);
        }
    }

    public function delete_image(Request $request, $albumID, $imageID){
        $xml = simplexml_load_string($request->getContent());
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $Token = Account::where('Token', $token) -> first();
        $Album = Album::where('id2', $albumID) ->where('AccountToken', $token) -> first();
        if(isset($Token) && isset($Album)){
            $Image = Image::where('id2', $imageID) -> where('AlbumId', $Album->id2) -> first();
            if(isset($Image)){
                do{
                    $DeleteToken = '';
                    for($i = 0 ; $i < 16 ; $i ++){
                        $rand = rand(0, 2);
                        if($rand == 0){
                            $chr = chr(rand(97, 122));
                            $DeleteToken = $DeleteToken.$chr;
                        }
                        if($rand == 1){
                            $chr = chr(rand(65, 90));
                            $DeleteToken = $DeleteToken.$chr;
                        }
                        if($rand == 2){
                            $DeleteToken = $DeleteToken.rand(0, 9);
                        }
                    }

                    $CheckDeleteToken = Image::where('DeleteToken', $DeleteToken) -> count();
                }while($CheckDeleteToken > 0);

                $Image->AlbumId = "";
                $Image->DeleteToken = $DeleteToken;
                $Image->save();

                $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
                $response_xml = $response_xml.'<data success="1" status="200">';
                $response_xml = $response_xml.'<delete_token>'.$DeleteToken.'</delete_token>';
                $response_xml = $response_xml.'</data>';

                return response($response_xml)
                    ->header('Content-Type', 'application/xml')
                    ->header('Authorization', 'Token '.$token);
            }
        }
    }

    public function recovery(Request $request){
        $xml = simplexml_load_string($request->getContent());
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $Token = Account::where('Token', $token) -> first();
        $Image = Image::where('DeleteToken', $xml->delete_token) -> first();
        $Album = Album::where('id2', $xml->dst_album) -> first();
        if(isset($Token) && isset($Image) && isset($Album)){ 
            $Image->AlbumId = $xml->dst_album;
            $Image->DeleteToken = "";
            $Image->save();

            $response_xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $response_xml = $response_xml.'<data success="1" status="204"/>';

            return response($response_xml)
                ->header('Content-Type', 'application/xml')
                ->header('Authorization', 'Token '.$token);
        }
    }

    /*public function update_image(Request $request, $albumID, $imageID){
        $data = $request->getContent();
        return $data->title;
    }*/

    public function latest(Request $request, $albumID){
        $xml = simplexml_load_string($request->getContent());
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $Token = Account::where('Token', $token) -> first();
        $Albums = Image::where('AlbumId', 'Eqin4SG') ->orderBy('created_at', 'asc') -> limit(1) -> get();

        $response = "";
        foreach($Albums as $Album){
            $response = $response.$Album->id2."|";
        }
        
        return $response;
    }

    public function test(){
        $img = imagecreatefromjpeg(base_path()."/images/"."6fs112o7He.jpg");
        $src_w = imagesx($img);
        $src_h = imagesy($img);

        $thumb_img = imagecreatetruecolor(90, 90);

        $des1 = imagecreatefromjpeg(base_path()."/images/"."zau2734xdY.jpg");
        $des1_w = imagesx($des1);
        $des1_h = imagesy($des1);

        imagecopyresampled($thumb_img, $img, 0, 0, 0, 0, 90, 90, $src_w, $src_h);
        
        imagecopyresampled($thumb_img, $des1, 0, 45, 0, 0, 45, 50, $des1_w, $des1_h);

        imagejpeg($thumb_img, base_path().'temp.jpg');

        $new_image = file_get_contents(base_path().'temp.jpg');

        return response($new_image)
            ->header('Content-Type', 'image/jpeg');
    }
}