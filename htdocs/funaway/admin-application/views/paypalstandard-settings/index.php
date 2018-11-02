<?php
defined('SYSTEM_INIT') or die(t_lang('INVALID_ACCESS')); 

$frm->setFormTagAttribute('id', 'payPalSettingsFrm');
$frm->setValidatorJsObjectName('payPalSettingsValidator');
$frm->setFormTagAttribute('onsubmit', 'updatePayPalSettings(this, payPalSettingsValidator); return false;');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->developerTags['fld_default_col'] = 12;
$frm->developerTags['colClassPrefix'] = 'col-md-';

$mainDiv = new HtmlElement('div', array('class'=>'fixed_container'));

$sectionmid = $mainDiv->appendElement('section', array('class'=>'section'));
$sectionmiddiv = $sectionmid->appendElement('div', array('class'=>'sectionhead'));
$sectionmiddiv->appendElement('h4', array(), 'Payment Method Settings - '.$payment_settings['pmethod_name'], true);

$sectionmidbodydiv = $sectionmid->appendElement('div', array('class'=>'sectionbody space'));
$sectionmidbodydiv->appendElement('div', array('class'=>'formhorizontal'),$frm->getFormHtml(), true);
echo $mainDiv->getHtml(); 
?>