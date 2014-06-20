<?php
namespace Ipmpdf\Service;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\ViewModel;
/**
 * Description of IpmpdfService
 *
 * @author aqnguyen
 */
class IpmpdfService implements EventManagerAwareInterface
{
    protected $doctrineEntityManager;
    protected $dbaDoctrineManager;
    protected $viewRenderer;
    protected $fileName;
    protected $data;
    protected $domainService;
    protected $messages = array();

    public function __construct($doctrineManager)
    {
        $this->setDoctrineEntityManager($doctrineManager);
    }
    
    public function create($template, $data, $targetPath = 'domain.pdf', $extendedContent = false)
    {
        $html2Pdf = new \HTML2PDF('L', 'A4', 'de', true, 'UTF-8', array(10, 5, 5, 5));
        $html2Pdf->setDefaultFont('Arial');
        $model = new ViewModel(
            array(
                'customers' => $data,
                'extendedContent' => $extendedContent,
                'messages' => $this->messages
            ));
        $model->setTemplate($template);
 
        $html = $this->viewRenderer->render($model);
        $html2Pdf->writeHTML($html);
        echo $html2Pdf->Output($targetPath);
        exit();
    }
    
    public function getCustomers()
    {
        $customerService = new CustomerService($this->doctrineEntityManager, $this->dbaDoctrineManager);
        $data = FileService::getFileContent($this->fileName);
        $customers = $customerService->generateCustomers($data, $this->domainService);
        $this->messages = array_merge($this->messages, $customerService->getMessages(), $this->domainService->getMessages());
        return $customers;
    }
    
    public function getDoctrineEntityManager()
    {
        return $this->doctrineEntityManager;
    }

    public function setDoctrineEntityManager($doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

   
    
    public function getEventManager()
    {
        
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        
    }
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    public function setViewRenderer($viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function getDbaDoctrineManager()
    {
        return $this->dbaDoctrineManager;
    }

    public function setDbaDoctrineManager($dbaDoctrineManager)
    {
        $this->dbaDoctrineManager = $dbaDoctrineManager;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }
    
    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function getDomainService()
    {
        return $this->domainService;
    }

    public function setDomainService($domainService)
    {
        $this->domainService = $domainService;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }


}
