<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    public function store()
    {
        // get all axie cards
        $cards = Http::get('https://storage.googleapis.com/axie-cdn/game/cards/card-abilities.json')->json();
        $card = array_shift($cards);

        foreach ($cards AS $card) {
            $class = substr($card['id'], 0, strpos($card['id'], '-'));

            switch ($class) {
                case 'aquatic':
                    $class = 'aqua';
                    break;
            }

            try {
                $stat_bg = Image::make('public/images/bg-'.$class.'.png');
                $stat_bg->resize(70, 70);

                $atk_icon = Image::make('public/images/icon-atk.png');
                $atk_icon->resize(50, 50);

                $def_icon = Image::make('public/images/icon-def.png');
                $def_icon->resize(50, 50);

                $effect_icon = Image::make('https://storage.googleapis.com/axie-cdn/game/cards/effect-icons/'.$card['iconId'].'.png');
                $effect_icon->resize(30, 30);

                $base_image = Image::make('https://storage.googleapis.com/axie-cdn/game/cards/base/'.$card['id'].'.png');
                $base_image->resizeCanvas(320, null, 'right');

                $base_image->insert($stat_bg->encode(), 'center', -125, -80);
                $base_image->insert($atk_icon->encode(), 'center', -125, -80);

                $base_image->insert($stat_bg->encode(), 'center', -125, -15);
                $base_image->insert($def_icon->encode(), 'center', -125, -15);

                $base_image->text($card['skillName'], 190, 52, function ($font) {
                    $font->file(public_path('fonts/changa-one.ttf'));
                    $font->size(22);
                    $font->color('#ffff');
                    $font->align('center');
                });

                $base_image->text($card['defaultEnergy'], 63, 53, function ($font) {
                    $font->file(public_path('fonts/changa-one.ttf'));
                    $font->size(44);
                    $font->color('#ffff');
                    $font->align('center');
                });

                $base_image->text($card['defaultAttack'], 35, 128, function ($font) {
                    $font->file(public_path('fonts/changa-one.ttf'));
                    $font->size(28);
                    $font->color('#ffff');
                    $font->align('center');
                });

                $base_image->text($card['defaultDefense'], 35, 195, function ($font) {
                    $font->file(public_path('fonts/changa-one.ttf'));
                    $font->size(28);
                    $font->color('#ffff');
                    $font->align('center');
                });

                $base_image->insert($effect_icon->encode(), 'center', -110, 128);

                $base_image->text(wordwrap($card['description'], 25), 180, 330, function ($font) {
                    $font->file(public_path('fonts/monsterrat.ttf'));
                    $font->size(14);
                    $font->color('#ffff');
                    $font->align('center');
                    $font->valign('center');
                });

                Storage::put('barebones/'.$card['skillName'].'.png', (string) $base_image->encode());
            } catch (\Exception $e) {
                logger($e);
            }
        }
    }
}
