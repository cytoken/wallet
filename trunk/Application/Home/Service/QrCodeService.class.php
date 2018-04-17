<?php
/**
 * Created by PhpStorm.
 * User: fealr
 * Date: 2017/12/25 14:23
 */

namespace Home\Service;
use OSS\OssClient;
use OSS\Core\OssException;

class QrCodeService extends BaseService
{
    public function createQrCode($code) {

        $qrStr = C('QRCODE_VIEW_PATH') . $code;
        $this->loger('$qrStr',$qrStr);
        $fileName = md5(uniqid(md5(microtime(true)), true)) . ".jpg";
        $filePath = C("QRCODE_TEMP_PATH") . $fileName;
        $logo = C("QRCODE_TEMP_LOGO");
        ob_clean();
        Vendor('phpqrcode.phpqrcode');
        $object = new \QRcode();
        $object::png($qrStr, $filePath, 'h', '10', 2);
        $QR = imagecreatefromstring(file_get_contents($filePath));//imagecreatefromstring:创建一个图像资源从字符串中的图像流
        $logo = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);// 获取图像宽度函数
        $QR_height = imagesy($QR);//获取图像高度函数
        $logo_width = imagesx($logo);// 获取图像宽度函数
        $logo_height = imagesy($logo);//获取图像高度函数
        $logo_qr_width = $QR_width / 4;//logo的宽度
        $scale = $logo_width / $logo_qr_width;//计算比例
        $logo_qr_height = $logo_height / $scale;//计算logo高度
        $from_width = ($QR_width - $logo_qr_width) / 2;//规定logo的坐标位置
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        Header("Content-type: image/png");
        //$url:定义生成带logo的二维码的地址及名称

        imagepng($QR, $filePath);
        $this->loger('uploadQr',$this->uploadQr($filePath));
        return $this->uploadQr($filePath);
    }

    public function uploadQr($fileName) {
        try {

            $this->loger("execute", "upload()");
            if (empty($fileName)) {
                $this->error = '没有上传的文件！';
            }

            // 同步到OSS
            Vendor('OSS.autoload');
            $ossClient = new OssClient(C('OSS_ACCESS_KEY_ID'), C('OSS_ACCESS_KEY_SECRET'), C('OSS_ENDPOINT'));

            $this->loger("headImgKey", $fileName);
            $content = file_get_contents($fileName);

            $newFileName = md5(uniqid(md5(microtime(true)), true)) . ".jpg";

            $ossClient->putObject(C('OSS_BUCKET'), $newFileName, $content);

            $license = C('OSS_URL') . $newFileName;
            $this->loger("license", $license);
            $data['code'] = 0;
            $data['CDNPath'] = $license;
            $data['localPath'] = $fileName;
            $this->loger("data", $data);
            return $license;
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return false;
        }
    }

}