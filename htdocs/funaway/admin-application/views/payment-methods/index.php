<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); 

$mainDiv = new HtmlElement('div', array('class'=>'fixed_container'));

$sectionmid = $mainDiv->appendElement('section', array('class'=>'section'));
$sectionmiddiv = $sectionmid->appendElement('div', array('class'=>'sectionhead'))->appendElement('div', array('class'=>'formhorizontal'),$srchFrm->getFormHtml(), true);
$sectionmiddiv->appendElement('h4', array(), 'Manage - Payment Methods', true);

/* $sectiontop->appendElement('div', array('class'=>'search-box sectionbody space togglewrap', 'style'=>'display:none;'))->appendElement('div', array('class'=>'formhorizontal'),$srchFrm->getFormHtml(), true);
echo $sectiontop->getHtml(); */

$sectionmidbodydiv = $sectionmid->appendElement('div', array('class'=>'sectionbody'));
$sectionmidbodydiv->appendElement('div', array('class'=>'formhorizontal'))->appendElement('div', array('id'=>'payment-methods-list'), '', true);
echo $mainDiv->getHtml();
?>