<?php


class Recipe4livingMappController extends ClientFrontendController
{
    public function __construct($args)
    {
        parent::__construct($args);
        if(array_search($_SERVER['REMOTE_ADDR'], array('127.0.0.1','216.48.124.142','66.54.186.254')) === false)exit('Not Allowed!');
    }

    public function view()
    {
     
    }
    public function check()
    {
        echo 'ok';
        echo json_encode($_GET);
    }
    
    
    public function search()
    {
        $search = base64_decode(Request::getString('search'));
        $itemsModel = BluApplication::getModel('items');
        $result = $itemsModel->textSearch($search);
        echo json_encode($result);
    }

}

?>
