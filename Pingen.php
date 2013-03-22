<?php

    /**
     * A class to use the API of pingen.com as an integrator
     *
     * For more information about Pingen and how to use it as an integrator see
     * https://pingen.com/en/customer/integrator/Briefversand-für-Integratoren.html
     *
     * API documentation can be found here:
     * https://www.pingen.com/en/developer.html
     *
     *
     * Copyright (c) 2013 by Pingen.com
     * Permission is hereby granted, free of charge, to any person obtaining a
     * copy of this software and associated documentation files (the "Software"),
     * to deal in the Software without restriction, including without limitation
     * the rights to use, copy, modify, merge, publish, distribute, sublicense,
     * and/or sell copies of the Software, and to permit persons to whom the
     * Software is furnished to do so, subject to the following conditions:
     * The above copyright notice and this permission notice shall be included
     * in all copies or substantial portions of the Software.
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
     * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
     * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
     * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
     * OR OTHER DEALINGS IN THE SOFTWARE.
     *
     * @link https://www.pingen.com/en/developer.html
     */


    class Pingen
    {
        /**
         * @var string Base URL of Pingen API
         */
        protected $sBaseURL = 'https://api.pingen.com/';

        /**
         * @var array Available connection methods
         */
        protected $aConnectionMethods = array(
            'curl',
            'fsocket'
        );

        /**
         * @var string Auth token
         */
        private $sToken;

        /**
         * @var string Used connection method
         */
        private $sConnectionMethod;

        /**
         * Constructor of class
         *
         * @param string $sToken Auth token
         * @param string $sConnectionMethod Connection method
         * @throws Exception Wrong connection method
         */
        public function __construct($sToken, $sConnectionMethod = 'curl')
        {
            $this->sToken = $sToken;

            if (!in_array($sConnectionMethod, $this->aConnectionMethods))
            {
                throw new Exception("Invalid connection method chosen, currently we accept following methods: " . implode(', ', $this->aConnectionMethods));
            }
            else
            {
                $this->sConnectionMethod = $sConnectionMethod;
            }
        }

        /**
         * List your available documents
         *
         * See https://www.pingen.com/en/developer/endpoints-documents.html for available options
         *
         * @param array $aOptions
         * @return string (json)
         */
        public function listDocuments($aOptions = array())
        {
            return $this->execute('document/list', $aOptions);
        }

        /**
         * Get information about a specific document
         *
         * @param int $iDocumentId
         * @return string (json)
         */
        public function getDocument($iDocumentId)
        {
            return $this->execute('document/get', array('id' => $iDocumentId));
        }

        /**
         * Download a specific document as pdf
         *
         * @param int $iDocumentId
         * @return application/pdf
         */
        public function getDocumentPdf($iDocumentId)
        {
            return $this->execute('document/pdf', array('id' => $iDocumentId));
        }

        /**
         * Preview a specific document as png
         *
         * See https://www.pingen.com/en/developer/endpoints-documents.html for available options
         *
         * @param int $iDocumentId
         * @return image/png
         */
        public function getDocumentPreview($iDocumentId, $aOptions = array())
        {
            $aData = array(
                'id'   => $iDocumentId,
                'data' => json_encode($aOptions)
            );
            return $this->execute('document/preview', $aData);
        }

        /**
         * Delete a specific document
         *
         * @param int $iDocumentId
         * @return string (json)
         */
        public function deleteDocument($iDocumentId)
        {
            return $this->execute('document/delete', array('id' => $iDocumentId));
        }

        /**
         * Send a specific document
         *
         * See https://www.pingen.com/en/developer/endpoints-documents.html for available options
         *
         * @param int $iDocumentId
         * @param array $aOptions
         * @return string (json)
         */
        public function sendDocument($iDocumentId, $aOptions = array(
            'speed' => 2,
            'color' => 0
        ))
        {
            $aData = array(
                'id'   => $iDocumentId,
                'data' => json_encode($aOptions)
            );
            return $this->execute('document/send', $aData);
        }

        /**
         * Upload a new file (and optionally send it right away)
         *
         * See https://www.pingen.com/en/developer/endpoints-documents.html for available options
         *
         * @param string $sFile
         * @param array $aOptions
         * @return string (json)
         */
        public function uploadDocument($sFile, $aOptions = array())
        {
            $sFile = realpath($sFile);
            $aData = array(
                'file' => '@' . $sFile,
                'data' => json_encode($aOptions)
            );
            return $this->execute('document/upload', $aData);
        }

        /**
         * You can list your available letters
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLimit Limit the amount of results
         * @param int $iPage When limiting the results, specifies page
         * @param string $sSort Sorts the list by the available values
         * @param string $sSortType Defines the way of sorting
         * @return mixed
         */
        public function listLetters($iLimit = 0, $iPage = 1, $sSort = 'recipient', $sSortType = 'asc')
        {
            return $this->execute("letter/list/limit/$iLimit/page/$iPage/sort/$sSort/sorttype/$sSortType");
        }

        /**
         * You can get your letter object
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLetterId The Id of the letter
         * @return mixed
         */
        public function getLetter($iLetterId)
        {
            return $this->execute("letter/list/get/id/$iLetterId");
        }

        /**
         * You can add new letter
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param array $aData Body parameters
         * @return mixed
         */
        public function addLetter($aData)
        {
            return $this->execute("letter/add", $aData);
        }

        /**
         * You can edit letter
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLetterId The id of the letter
         * @param array $aData Body Parameters
         * @return mixed
         */
        public function editLetter($iLetterId, $aData)
        {
            return $this->execute("letter/edit/id/$iLetterId", $aData);
        }

        /**
         * You can get letter preview
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLetterId The id of the letter
         * @param int $iPage The page of the letter to grab as preview
         * @param int $iSize The width of preview
         * @return mixed
         */
        public function getLetterPreview($iLetterId, $iPage = 1, $iSize = 595)
        {
            return $this->execute("letter/preview/id/$iLetterId/page/$iPage/size/$iSize");
        }

        /**
         * You can get letter as pdf
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLetterId The id of the letter
         * @return mixed
         */
        public function getLetterPdf($iLetterId)
        {
            return $this->execute("letter/pdf/id/$iLetterId");
        }

        /**
         * You can send letter
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLetterId The id of the letter
         * @param array $aData Body Parameters
         * @return mixed
         */
        public function sendLetter($iLetterId, $aData)
        {
            return $this->execute("letter/send/id/$iLetterId", $aData);
        }

        /**
         * You can delete letter
         *
         * See https://www.pingen.com/en/developer/endpoints-letters.html
         *
         * @param int $iLetterId The id of the letter
         * @return mixed
         */
        public function deleteLetter($iLetterId)
        {
            return $this->execute("letter/delete/id/$iLetterId");
        }

        /**
         * You can list your available post sends
         *
         * See https://www.pingen.com/en/developer/endpoints-posts.html
         *
         * @param int $iLimit Limit the amount of results
         * @param int $iPage When limiting the results, specifies page
         * @param string $sSort Sorts the list by available values
         * @param string $sSortType Defines the way of sorting
         * @return mixed
         */
        public function  listPosts($iLimit = 0, $iPage = 1, $sSort = 'date', $sSortType = 'desc')
        {
            return $this->execute("post/list/limit/$iLimit/page/$iPage/sort/$sSort/sorttype/$sSortType");
        }

        /**
         * You can get your post object
         *
         * See https://www.pingen.com/en/developer/endpoints-posts.html
         *
         * @param int $iDocumentId The Id of the post sending
         * @return mixed
         */
        public function getPost($iDocumentId)
        {
            return $this->execute("post/get/id/$iDocumentId");
        }

        /**
         * You can cancel post
         *
         * See https://www.pingen.com/en/developer/endpoints-posts.html
         *
         * @param int $iDocumentId The Id of the post sending
         * @return mixed
         */
        public function cancelPost($iDocumentId)
        {
            return $this->execute("post/cancel/id/$iDocumentId");
        }

        /**
         * You can list your queue
         *
         * See https://www.pingen.com/en/developer/endpoints-queue.html
         *
         * @param int $iLimit Limit the amount of results
         * @param int $iPage When limiting the results, specifies page
         * @param string $sSort Sorts the list by available values
         * @param string $sSortType Defines the way of sorting
         * @return mixed
         */
        public function listQueue($iLimit = 0, $iPage = 1, $sSort = 'date', $sSortType = 'desc')
        {
            return $this->execute("queue/list/limit/$iLimit/page/$iPage/sort/$sSort/sorttype/$sSortType");
        }

        /**
         * You can get your queue
         *
         * See https://www.pingen.com/en/developer/endpoints-queue.html
         *
         * @param int $iDocumentId The Id of the queue entry
         * @return mixed
         */
        public function getQueue($iDocumentId)
        {
            return $this->execute("queue/get/id/$iDocumentId");
        }

        /**
         * You can cancel a pending queue entry
         *
         * See https://www.pingen.com/en/developer/endpoints-queue.html
         *
         * @param int $iDocumentId The Id of the queue entry
         * @param array $aData Body Parameters
         * @return mixed
         */
        public function cancelQueue($iDocumentId, $aData)
        {
            return $this->execute("queue/cancel/id/$iDocumentId", $aData);
        }

        /**
         * You can list your available contacts
         *
         * See https://www.pingen.com/en/developer/endpoints-contacts.html
         *
         * @param int $iLimit Limit the amount of results
         * @param int $iPage When limiting the results, specifies page
         * @param string $sSort Sorts the list by available values
         * @param string $sSortType Defines the way of sorting
         * @return mixed
         */
        public function listContacts($iLimit = 0, $iPage = 1, $sSort = 'date', $sSortType = 'desc')
        {
            return $this->execute("contact/list/limit/$iLimit/page/$iPage/sort/$sSort/sorttype/$sSortType");
        }

        /**
         * You can get your document
         *
         * See https://www.pingen.com/en/developer/endpoints-contacts.html
         *
         * @param int $iContactId The Id of the contact
         * @return mixed
         */
        public function getContact($iContactId)
        {
            return $this->execute("contact/get/id/$iContactId");
        }

        /**
         * You can add new contact
         *
         * See https://www.pingen.com/en/developer/endpoints-contacts.html
         *
         * @param array $aData Body parameters
         * @return mixed
         */
        public function addContact($aData)
        {
            return $this->execute("contact/add", $aData);
        }

        /**
         * You can edit new contact
         *
         * See https://www.pingen.com/en/developer/endpoints-contacts.html
         *
         * @param int $iContactId The Id of the contact
         * @param array $aData Body parameters
         * @return mixed
         */
        public function editContact($iContactId, $aData)
        {
            return $this->execute("contact/edit/id/$iContactId", $aData);
        }

        /**
         * You can delete a contact
         *
         * See https://www.pingen.com/en/developer/endpoints-contacts.html
         *
         * @param int $iContactId The Id of the contact
         * @return mixed
         */
        public function deleteContact($iContactId)
        {
            return $this->execute("contact/delete/id/$iContactId");
        }

        /**
         * @param string $sKeyword
         * @param array $aData
         * @return mixed
         */
        private function execute($sKeyword, $aData = array())
        {

            //prepare url
            $aURLParts = array(
                $this->sBaseURL,
                $sKeyword,
                'token',
                $this->sToken
            );
            $sURL      = implode('/', $aURLParts);

            //$data['data'] must not be empty
            if (isset($aData['data']) && (count(json_decode($aData['data'])) == 0 || $aData['data'] == ''))
            {
                unset($aData['data']);
            }

            $jsonResponse = false;

            //send request
            switch ($this->sConnectionMethod)
            {
                case 'curl':
                    $jsonResponse = $this->execute_curl($sURL, $aData);
                    break;
            }

            $objResponse = json_decode($jsonResponse);
            if ($objResponse->error)
            {
                throw new Exception($objResponse->errormessage, $objResponse->errorcode);
            }
            else
            {
                return $objResponse;
            }
        }

        /**
         * Execute api call using curl
         *
         * @param string $sURL Endpoint to execute
         * @param array $aData Post data to send
         * @return mixed
         * @throws Exception Connection error
         */
        private function execute_curl($sURL, $aData)
        {
            try
            {
                $objCurlConn = curl_init();
                curl_setopt($objCurlConn, CURLOPT_URL, $sURL);
                curl_setopt($objCurlConn, CURLOPT_POST, 1);
                curl_setopt($objCurlConn, CURLOPT_POSTFIELDS, $aData);
                curl_setopt($objCurlConn, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($objCurlConn, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($objCurlConn, CURLOPT_SSL_VERIFYPEER, 0);
                return curl_exec($objCurlConn);
            } catch (Exception $e)
            {
                throw new Exception("Error occurred in curl connection");
            }
        }
    }