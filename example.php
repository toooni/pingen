<?php

include "Pingen.php";

$apiPingen = new Pingen('mytoken');

$objDocuments = $apiPingen->documents_list();

echo $objDocuments->items[0]->id;

var_dump($apiPingen->documents_get($objDocuments->items[0]->id));