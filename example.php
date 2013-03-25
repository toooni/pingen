<?php

include "Pingen.php";

$objPingen = new Pingen('mytoken');

try {
//    $objDocuments = $objPingen->uploadDocument('example.pdf', array('send' => 1));

//    $objDocuments = $objPingen->addLetter(array(
//        'recipients' => array(
//            array(
//                'name' => 'David Peterson',
//                'address' => "Longstreet 4\n8005 Zürich\nSwitzerland"
//            ),
//        ),
//        'place' => 'Zürich',
//        'date' => '2013-03-20',
//        'title' => 'Testdocument',
//        'content' => "This is my multiline content<br>fully <i>capable</i> of <b>html</b>"
//    ));

//    header('Content-Type: application/pdf');

    $objDocuments = $objPingen->account_plan();

    var_dump($objDocuments);
}catch (Exception $e)
{
    echo 'An error occured with number <b>' . $e->getCode() . '</b> and message <b>' . $e->getMessage() . '</b>';
}