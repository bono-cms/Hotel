<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Hotel\Controller;

use Site\Controller\AbstractController;

final class Room extends AbstractController
{
    /**
     * Performs a search
     * 
     * @return string
     */
    public function searchAction()
    {
        if ($this->request->hasQuery('checkin', 'checkout', 'criteria')) {
            // Query parameters
            $checkin = $this->request->getQuery('checkin');
            $checkout = $this->request->getQuery('checkout');
            $criteria = $this->request->getQuery('criteria');

            $this->loadSitePlugins();
            $this->view->getBreadcrumbBag()
                       ->addOne($this->translator->translate('Search'));

            $title = $this->translator->translate('Search results');

            // Dummy page
            $page = new VirtualEntity();
            $page->setTitle($title)
                 ->setName($title)
                 ->setMetaDescription(null);

            return $this->view->render('rooms', [
                'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true),
                'rooms' => $this->getModuleService('roomService')->search($checkin, $checkout, $criteria),
                'page' => $page
            ]);

        } else {
            return false;
        }
    }
}
