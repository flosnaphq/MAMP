<?php

class MyHelper extends FatModel {

  
    static function getStatus() {
        return array(1 => "Active", 0 => "Inactive");
    }

    static function getStatusByKey($key) {
        $arr = MyHelper::getStatus();
        return $arr[$key];
    }

    static function getInnovaEditorObj($textarea_id, $div_id, $js_tag = true) {
        $innova_obj = 'window["site_' . $textarea_id . '"] = new InnovaEditor("site_' . $textarea_id . '");
					window["site_' . $textarea_id . '"].mode = "XHTMLBody";

					window["site_' . $textarea_id . '"].width = "100%";
					window["site_' . $textarea_id . '"].groups = [
					["group1", "", ["Bold", "Italic", "Underline", "FontDialog", "ForeColor", "TextDialog", "RemoveFormat"]],
					["group2", "", ["Bullets", "Numbering", "JustifyLeft", "JustifyCenter", "JustifyRight"]],
					["group3", "", ["LinkDialog"]],
					["group4", "", ["Undo", "Redo", "FullScreen", "SourceDialog","ImageDialog"]]
					];
					
					window["site_' . $textarea_id . '"].returnKeyMode = 2;
					window["site_' . $textarea_id . '"].REPLACE("' . $textarea_id . '", "' . $div_id . '");
					window["site_' . $textarea_id . '"] .fileBrowser = "/admin/innova/assetmanager/asset.php";
					';

        if ($js_tag) {
            $innova_obj = '<script>' . $innova_obj . '</script>';
        }
        return $innova_obj;
    }

    public static function backgroundColor($firstLetter) {
        $range1 = array("A", "G", "M", "S", 'a', 'g', 'm', 's');
        $range2 = array("B", "H", "N", "T", 'b', 'h', 'n', 't');
        $range3 = array("C", "I", "O", "U", 'c', 'i', 'o', 'u');
        $range4 = array("D", "J", "P", "V", "X", 'd', 'j', 'p', 'v', 'x');
        $range5 = array("E", "K", "Q", "W", "Z", 'e', 'k', 'q', 'w', 'z');
        $range6 = array("F", "L", "R", "Y", 'f', 'l', 'r', 'y');
        //	$firstLetter ." ";
        if (in_array($firstLetter, $range1)) {
            return 'red';
        } else if (in_array($firstLetter, $range2)) {
            return 'purple';
        } else if (in_array($firstLetter, $range3)) {
            return 'green';
        } else if (in_array($firstLetter, $range4)) {
            return 'blue';
        } else if (in_array($firstLetter, $range5)) {
            return 'red';
        } else if (in_array($firstLetter, $range6)) {
            return 'yellow';
        }
    }

    static function getPasswordForm() {
        $frm = new Form('passwordForm');
        $frm->addHiddenField('', 'user_id');
        $frm->addPasswordField('New Password', 'user_password')->requirements()->setRequired();
        $cpwd = $frm->addPasswordField('Confirm Password', 'cpassword');
        $cpwd->requirements()->setRequired();
        $cpwd->requirements()->setCompareWith('user_password', 'eq');
        $frm->addSubmitButton('&nbsp;', 'submit_btn', 'Update');
        return $frm;
    }

}
