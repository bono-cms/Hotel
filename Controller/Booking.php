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

use Krystal\Stdlib\VirtualEntity;
use Krystal\Validate\Pattern;
use Payment\Controller\PaymentTrait;
use Payment\Collection\ResponseCodeCollection;
use Site\Controller\AbstractController;
use Hotel\Collection\BookingStatusCollection;
use Hotel\Service\BookingService;

final class Booking extends AbstractController
{
    use PaymentTrait;

    /**
     * {@inheritDoc}
     */
    protected function bootstrap($action)
    {
        // Disabled CSRF for gateway action
        if ($action === 'responseAction') {
            $this->enableCsrf = false;
        }

        parent::bootstrap($action);
    }

    /**
     * Returns shared booking data
     * 
     * @param float $price Price per nighg
     * @param string $checkin
     * @param string $checkout
     * @param int $guests
     * @return array
     */
    private function getData($price, $checkin, $checkout, $guests)
    {
        $duration = BookingService::getDuration($checkin, $checkout);
        $amount = $duration * $price;

        $tax = (3 * $amount / 100); // Static

        return [
            'duration' => $duration,
            'currency' => 'USD',
            'check-in' => [
                'date' => $checkin,
                'time' => '14:00'
            ],
            'check-out' => [
                'date' => $checkout,
                'time' => '16:00'
            ],
            'guests' => $guests,
            'tax' => $tax,
            'total' => ($amount + $tax),
            'charge' => $amount
        ];
    }

    /**
     * Renders booking form
     * 
     * @return string
     */
    public function indexAction()
    {
        if ($this->request->hasQuery('checkin', 'checkout', 'room_id')) {
            // Query variables
            $roomId = $this->request->getQuery('room_id');
            $checkin = $this->request->getQuery('checkin');
            $checkout = $this->request->getQuery('checkout');
            $guests = $this->request->getQuery('guests', 0);

            // Find room by its ID
            $room = $this->getModuleService('roomService')->fetchById($roomId, false);

            if ($room !== false) {
                $page = new VirtualEntity();
                $page->setTitle('Booking a room')
                     ->setSeo(false);

                // Load view plugins
                $this->loadSitePlugins();

                return $this->view->render('hotel-booking', [
                    'room' => $room,
                    'data' => $this->getData($room->getPrice(), $checkin, $checkout, $guests),
                    'page' => $page,
                    'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true)
                ]);
            }
        } else {
            // Trigger 404 Error
            return false;
        }
    }

    /**
     * Performs a booking
     * 
     * @return string
     */
    public function bookAction()
    {
        $group = 'booking';

        // Raw POST data
        $request = $this->request->getPost();

        $formValidator = $this->createValidator([
            'input' => [
                'source' => $request[$group],
                'definition' => [
                    'client' => new Pattern\Name,
                    'email' => new Pattern\Email,
                    'phone' => new Pattern\Phone
                ]
            ]
        ]);

        if ($formValidator->isValid()) {
            $data = $request[$group];

            // Create temporary booking
            $token = $this->getModuleService('bookingService')->save($data, BookingStatusCollection::STATUS_TEMPORARY, $request['guest']);

            if ($token) {
                return $this->json([
                    'backUrl' => $this->createUrl('Hotel:Booking@gatewayAction', [$token])
                ]);
            } else {
                // Error on saving
            }

        } else {
            return $this->formatErrors($formValidator->getErrors(), $group);
        }
    }

    /**
     * Handle success or failure after payment gets done
     * 
     * @param string $token Unique transaction token
     * @return mixed
     */
    public function responseAction($token)
    {
        // Find transaction row by its token
        $transaction = $this->getModuleService('bookingService')->fetchByToken($token);
        $response = $this->createResponse('Prime4G');

        if ($response->canceled()) {
            return $this->renderResponse(ResponseCodeCollection::RESPONSE_CANCEL);
        } else {
            // Now confirm payment by token, since its successful
            $this->getModuleService('bookingService')->confirmByToken($token);
            return $this->renderResponse(ResponseCodeCollection::RESPONSE_SUCCESS);
        }
    }

    /**
     * Renders payment gateway
     * 
     * @param string $token
     * @return string
     */
    public function gatewayAction($token)
    {
        $transaction = $this->getModuleService('bookingService')->fetchByToken($token);

        if ($transaction) {
            $transaction['extension'] = 'Prime4G';
            return $this->renderGateway('Hotel:Booking@responseAction', $transaction);
        } else {
            return false;
        }
    }
    
    /**
     * Format error messages
     * 
     * @param string $error Error string
     * @param string $group Input group
     * @return string
     */
    private function formatErrors($errors, $group)
    {
        $errors = json_decode($errors, false);
        $output = [];

        foreach ($errors as $name => $messages) {
            $output[sprintf('%s[%s]', $group, $name)] = $messages;
        }

        return json_encode($output);
    }
}
