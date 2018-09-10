<?php
/**
 * Created by PhpStorm.
 * User: nightkid
 * Date: 2018/9/9
 * Time: 上午11:53
 */

namespace App\Http\Controllers;

use App\Model\Product;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public $cookie = "t=a66b0e4e515bf5b1ed518f4403b6d790; cookie2=179fd7487c47b82f765ebc35630d8e22; v=0; _tb_token_=38a833b7b73e; cna=LOYKFP0fGAICAXbB8SzmpLvZ; alimamapwag=TW96aWxsYS81LjAgKE1hY2ludG9zaDsgSW50ZWwgTWFjIE9TIFggMTBfMTNfNikgQXBwbGVXZWJLaXQvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lLzY4LjAuMzQ0MC4xMDYgU2FmYXJpLzUzNy4zNg%3D%3D; cookie32=c7eeb52e137a76702b3fbac3252488c0; alimamapw=SRYJBw9cXQhQAA1ROQECVAMOUlJTBlRRUwFUUV5RUwIAAwRaAlJVUAJVAF0E; cookie31=MTc4Nzk0NDEsenNtMTk4OTA2MjUwLDUyMzAxMzE4M0BxcS5jb20sVEI%3D; account-path-guide-s1=true; 17879441_yxjh-filter-1=true; taokeisb2c=; JSESSIONID=4D653D58D43406B096FA4198AC98D7E8; undefined_yxjh-filter-1=true; login=W5iHLLyFOGW7aA%3D%3D; apushb22dcc49aeef15b1b81afbc9c22a6475=%7B%22ts%22%3A1536490128116%2C%22parentId%22%3A1536490073087%7D; isg=BDAwYaYxH6cc3cOxXYxckNRlAfhC0RWlYvHoByqBZwtR5dWP0IjeUSETOa0g9cyb";
    public function runTb(Request $request)
    {
        set_time_limit(0);
        $pages = $request->input("page", 1);
        $keyword = $request->input("q", '');
        $page = explode(",", $pages);
        foreach ($page as $p) {
            echo $p . "<br>";
            flush();
            $data = $this->httpGetList($p,$keyword);
            if (empty($data)) {
                echo "end <br>" ;
                die;
            }
            $list = $data['pageList'];
            foreach ($list as $val) {
                sleep(rand(1, 5));
                $this->httpGetContent($val);
            }
        }

        echo "success-end <br>";die;
    }

    public function httpGetList($page, $q)
    {
        $t = microtime(true) * 1000;
        $url = 'https://pub.alimama.com/items/search.json?toPage='.$page.'&q='.$q.'&dpyhq=1&auctionTag=&perPageSize=50&shopTag=dpyhq&t='.$t.'&_tb_token_=38a833b7b73e';
        $cookie = $this->cookie;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "cookie: " . $cookie,
                "referer: https://pub.alimama.com/promo/search/index.htm?spm=a219t.7900221/1.1998910419.de727cf05.9dce75a5vcKa4y&toPage=1&queryType=2",
                "user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36",
                "x-requested-with: XMLHttpRequest"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        }
        $response = json_decode($response,true);
        return $response['data'] ?? '';
    }

    public function httpGetContent($info)
    {
        $auctionid = $info['auctionId'];

        //判断是否存在
        $productModel = new Product();
        $mInfo = $productModel->where(["auctionid" => $auctionid])->first();
        if ($mInfo) {
            return true;
        }

        $t = microtime(true) * 1000;
        $url = 'https://pub.alimama.com/common/code/getAuctionCode.json?auctionid='.$auctionid.'&adzoneid=79768871&siteid=23918222&scenes=1&tkFinalCampaign=20&t='.$t.'&_tb_token_=38a833b7b73e';
        $cookie = $this->cookie;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "cookie: " . $cookie,
                "referer: https://pub.alimama.com/promo/search/index.htm?spm=a219t.7900221/1.1998910419.de727cf05.9dce75a5vcKa4y&toPage=1&queryType=2",
                "user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36",
                "x-requested-with: XMLHttpRequest"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        }
        $response = json_decode($response,true);
        if (empty($response['data'])) {
            echo "content-end";
            die;
        }
        $data = [
            'name' => $info['title'],
            'url' => $info['auctionUrl'],
            'tao_token' => $response['data']['taoToken'] ?? '',
            'coupon_short_link_ur' => $response['data']['couponShortLinkUrl'] ?? '',
            'qr_code_url' => "https:" . $response['data']['qrCodeUrl'] ?? ''
        ];
        $productModel->auctionid = $auctionid;
        $productModel->name = strip_tags($data['name']);
        $productModel->url = $data['url'];
        $productModel->tao_token = $data['tao_token'];
        $productModel->coupon_short_link_ur = $data['coupon_short_link_ur'];
        $productModel->qr_code_url = $data['qr_code_url'];
        $productModel->pict_url = "https:" . $info['pictUrl'];
        $productModel->shop_title = $info['shopTitle'];
        $productModel->coupon_amount = $info['couponAmount'];
        $productModel->coupon_info = $info['couponInfo'];
        $productModel->zk_price = $info['zkPrice'];
        $productModel->coupon_tao_token = $response['data']['couponLinkTaoToken'];
        if (isset($response['data']['couponLinkTaoToken']) && !empty($response['data']['couponLinkTaoToken']) && !empty($data['tao_token'])) {
            $productModel->save();
        }


    }
}