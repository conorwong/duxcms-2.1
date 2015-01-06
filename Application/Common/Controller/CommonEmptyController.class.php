<?php
namespace Common\Controller;
use Home\Controller\SiteController;

class CommonEmptyController extends SiteController {

    public function index(){
        header('HTTP/1.1 404 Not Found'); 
		header("status: 404 Not Found"); 
		$this->siteDisplay('error_404');

    }
 }