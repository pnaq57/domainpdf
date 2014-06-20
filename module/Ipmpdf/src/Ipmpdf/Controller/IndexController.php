<?php
namespace Ipmpdf\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{
    protected $ipmpdfService;

    public function indexAction()
    {
        $files = glob('./data/domains/*');
        $csvDone = file_get_contents('./data/done');
        $csvDone = explode(';', $csvDone);
        return new ViewModel(
            array('files' => $files, 'csvList' => $csvDone)
        );
    }
    
    public function createpdfAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $this->ipmpdfService->setFileName($data->filename);
            $customers = $this->ipmpdfService->getCustomers();
            $messages = $this->ipmpdfService->getMessages();
            if (!$customers) {
                return;
            }
            if (isset($data->extendedContent)) {
                $this->ipmpdfService->create('ext-domain-invoice', $customers, null, true);
            } else {
                $this->ipmpdfService->create('domain-invoice', $customers);
            }
            
        }
    }
    
    public function pdfdoneAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (!empty($data->fileName) && !empty($data->checked)) {
                $csvDone = file_get_contents('./data/done');
                $csvDone = explode(';', $csvDone);
                if ($data->checked == 'true') {
                    if (($found = array_search($data->fileName, $csvDone)) === FALSE) {
                        $csvDone[] = $data->fileName;
                        $csvString = implode(';', $csvDone);
                        file_put_contents('./data/done', $csvString);
                    }
                } else {
                    if (($found = array_search($data->fileName, $csvDone)) !== FALSE) {
                        unset($csvDone[$found]);
                        $csvString = implode(';', $csvDone);
                        file_put_contents('./data/done', $csvString);
                    }
                }
            }
        }
        die;
    }


    public function setIpmpdfService($ipmpdfService)
    {
        $this->ipmpdfService = $ipmpdfService;
    }


  
}