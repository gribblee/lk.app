<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Helpers\BitrixApi;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    protected $bitrix24;

    function __construct()
    {
        $this->bitrix24 = new BitrixApi('189u3xsih7dzkd61'); //5zs1g2e9r9jquqgy');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request)
    {
        $tags = [];
        $template = [
            "UI/UX" => "Элементы интерфейса",
            "UX.Page" => "Страница с ошибкой",
            "UI.Balance" => "Баланс",
            "UX.Payment" => "Оплаты",
            "UI.Bonus" => "Бонусы",
            "UI.Setting" => "Настройки",
            "UI.Deals" => "Клиенты",
            "UI.Bids" => "Заявки",
            "UI.Regions" => "Регионы",
            "UI.Direction" => "Направления",
            "UI.Rate" => "Ставка",
            "UX.Logout" => "Выход",
            "UX.Insurance" => "Страховка",
            "API.Error" => "Странные ошибки",
        ];
        $i = 1;
        foreach ($request->categories as $tag) {
            $tags[] = "{$i}." . $template[$tag];
            $i++;
        }
        $tagDesc = '<b>' . implode("</b>\r\n<b>", $tags) . '</b>';
        $imgDesc = '';
        $path = implode(DIRECTORY_SEPARATOR, ['support', $request->user()->id, date("Ymd"), 'images']);
        if (Storage::disk('public')->listContents($path) > 0) {
            $date = date("Ymd");
            $imgDesc = "<a href=\"https://lk.leadz.monster/support/admin/images/{$request->user()->id}/{$date}\">Изображения по ссылке</a>";
        }

        Log::info($this->bitrix24
            ->task()
            ->req('FIELDS', [
                'TITLE' => "Fix {$request->user()->name}",
                'DESCRIPTION' => "<b>Общая информация</b>\r\nUser id: {$request->user()->id}\r\nEmail: {$request->user()->email}\r\nТелефон: {$request->user()->phone}\r\n\r\n<b>Описание</b>\r\n{$request->description}\r\n\r\nПроблемы:\r\n{$tagDesc}\r\n\r\n{$imgDesc}",
                'PRIORITY' => 1,
                'STATUS' => 2,
                'RESPONSIBLE_ID' => 29,
                'GROUP_ID' => 15,
                'STAGE_ID' => 0,
                'CREATED_BY' => 1,
                'AUDITORS' => [1]
            ])
            ->add());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $uid = 0;
        $images = [];
        $file = $request->file('file');
        $uid++;
        $path = implode(DIRECTORY_SEPARATOR, ['support', $request->user()->id, date("Ymd"), 'images']);
        $file->storeAs(
            $path,
            $file->getClientOriginalName(),
            ['disk' => 'public']
        );
        $filename = preg_replace('/\.\w+$/', '', $file->getClientOriginalName());
        $images[] = [
            'uid' => time(),
            'name' => $filename,
            'status' => 'done',
            'thumbUrl' => '',
            'url' => ''
        ];
        return response()->json($images);
    }

    /**
     * @param Request $request
     * @param $uid
     * @param $ymd
     * @return JsonResponse
     */
    public function images(Request $request, $uid, $ymd)
    {
        if ($request->user()->role === 'ROLE_ADMIN') {
            $path = implode(DIRECTORY_SEPARATOR, ['support', $uid, $ymd, 'images']);
            $files = Storage::disk('public')->listContents($path);
            $images = [];
            foreach ($files as $file) {
                if ($file['type'] == 'file') {
                    $filePath = Storage::disk('public')->path($file['path']);
                    $images[] = [
                        'ext' => $file['extension'] ?? '',
                        'mime_type' => mime_content_type($filePath),
                        'b64' => base64_encode(Storage::disk('public')->get($file['path']))
                    ];
                }
            }
            return response()->json($images);
        } else {
            return response()->json([], 404);
        }
    }
}
