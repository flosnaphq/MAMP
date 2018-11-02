<?php
defined('SYSTEM_INIT') or die(t_lang('INVALID_ACCESS')); 

$frm->setFormTagAttribute('id', 'editPaymentMethodForm');
$frm->setValidatorJsObjectName('paymentMethodSetupValidator');
$frm->setFormTagAttribute('onsubmit', 'updatePaymentMethod(this, paymentMethodSetupValidator); return false;');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->developerTags['fld_default_col'] = 12;

$htmlFld = $frm->getField('set_permission');
$htmlFld->developerTags['col'] = 3;
$htmlFld->value = '<div class="adhtmlhding">Set Permissions</h4>';

$submitBtn = $frm->getField('btn_submit');
$submitBtn->value = 'Update';
$mainDiv = new HtmlElement('div', array('class'=>'fixed_container'));

$sectionmid = $mainDiv->appendElement('section', array('class'=>'section'));
$sectionmiddiv = $sectionmid->appendElement('div', array('class'=>'sectionhead'));
$sectionmiddiv->appendElement('h4', array(), 'Update Payment Method', true);

$sectionmidbodydiv = $sectionmid->appendElement('div', array('class'=>'sectionbody space'));
$sectionmidbodydiv->appendElement('div', array('class'=>'formhorizontal'),$frm->getFormHtml(), true);
echo $mainDiv->getHtml();		
?>