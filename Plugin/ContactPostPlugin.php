<?php

namespace Yogendra\ContactForm\Plugin;


class ContactPostPlugin {

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;


    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder   
    ) {
        $this->messageManager = $messageManager;
        $this->urlInterface = $urlInterface;
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder; 
    }
    public function afterExecute(
        \Magento\Contact\Controller\Index\Post $subject,
        $result          
    )
    {
        $post = $subject->getRequest()->getPostValue();  
        if ($post) { 

            // here we can implement custom coding as per extending its functionality
            
            // Below I have static data related to email we can use dynamic data and can fetch from admin system part as peryou requirement

            try{
                $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
                $templateVars = array(
                                    'store' => $this->_storeManager->getStore(),
                                    'customer_name' => $post['name'], // customer name fill on contact form 
                                    'customer_email'	=> $post['email'] // email of filled in contact form
                                );
                $from = array('email' => "yoge.singh1011@gmail.com", 'name' => 'Yogendra');
                $this->inlineTranslation->suspend();
                $to = array('developer@yopmail.com');
                $transport = $this->_transportBuilder->setTemplateIdentifier('yoge_template') // Pass your email template id here
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($templateVars)
                                ->setFrom($from)
                                ->addTo($to)
                                ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            }catch(\Exception $e){
                throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
            }
        }
        return $result;
    }
}