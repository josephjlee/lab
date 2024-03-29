<?php
/**
 * Wraps all the Routing for the Result Module
 */

namespace App\Module;

class Result extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Result');
		$a->map(['GET','POST'], '/sync', 'App\Controller\Result\Sync');

		$a->get('/create', 'App\Controller\Result\Create');
		$a->post('/create/save', 'App\Controller\Result\Create:save');

		$a->get('/download', 'App\Controller\Result\Download');
		$a->map(['GET','POST'], '/upload', 'App\Controller\Result\Upload');
		$a->get('/upload/preview', 'App\Controller\Result\Upload:preview');
		$a->map(['GET','POST'], '/upload/queue', 'App\Controller\Result\Queue');

		$a->map([ 'GET', 'POST'], '/{id}', 'App\Controller\Result\View');
		$a->get('/{id}/sync', 'App\Controller\Result\Sync'); // @deprecated, post to /sync w/ID

	}
}
