<?php

require_once __DIR__ . '/../vendor/autoload.php';

use pingencom\Pingen;

try {
	$pingen = new Pingen('INSERTYOURTOKENHERE', Pingen::MODE_PRODUCTION);

	/* upload a document */
	$uploadResponse = $pingen->document_upload('src/example.pdf');

	/* retrieve document id */
	$documentId = $uploadResponse->id;

	/* send document via post */
	$sendResponse = $pingen->document_send($documentId, Pingen::SPEED_PRIORITY, Pingen::PRINT_COLOR);

	/* retrieve send id */
	$sendId = $sendResponse->id;

	/* check status of my sending */
	$sendObject = $pingen->send_get($sendId);

	/*
	 * to see all status codes go to: https://www.pingen.com/en/developer/objects-send.html
	 */
	$statusCode = $sendObject->item->status;

	echo "Success" . PHP_EOL;

}catch (Exception $e)
{
	echo 'An error occured with number <b>' . $e->getCode() . '</b> and message <b>' . $e->getMessage() . '</b>' . PHP_EOL;
}