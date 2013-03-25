<?php

include "Pingen.php";

$apiPingen = new Pingen('mytoken');

$objDocuments = $apiPingen->listDocuments();

echo $objDocuments->items[0]->id;

var_dump($apiPingen->getDocument($objDocuments->items[0]->id));