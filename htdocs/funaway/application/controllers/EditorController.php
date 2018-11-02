<?php

class EditorController extends FatController {

    private $common;
    private $task;

    function demoPhoto($image = "", $w = 0, $h = 0) {
        self::displayImage($image, 5, 5, true);
    }

    public function editorImage($img) {
        ob_end_clean();

        $pth = CONF_INSTALLATION_PATH . 'user-uploads/backend/' . $img;

        if (!is_file($pth)) {
            $pth = CONF_INSTALLATION_PATH . 'public/images/noimg.png';
        }

        /*  if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($pth))) {
          header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($pth)).' GMT', true, 304);
          exit;
          }

          header('Cache-Control: public');
          header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($pth)).' GMT', true, 200);
         */
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
        $ext = pathinfo($pth, PATHINFO_EXTENSION);
        if ($ext == "svg") {
            Helper::editorSvg($pth);
            exit;
        }

        $size = getimagesize($pth);

        if ($size) {
            list($w, $h) = getimagesize($pth);
        } else {
            /* $obj = new imageResize($pth);
              $obj->setMaxDimensions($w, $h);
              $obj->setResizeMethod(imageResize::IMG_RESIZE_EXTRA_ADDSPACE); */
        }



        $obj = new ImageResize($pth);
        $obj->setMaxDimensions($w, $h);
        $obj->setResizeMethod(imageResize::IMG_RESIZE_EXTRA_ADDSPACE);
        header("Content-Type: " . $size['mime']);
        $obj->displayImage();
    }

}
