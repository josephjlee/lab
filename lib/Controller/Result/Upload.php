<?php
/**
 * Accept Upload of Sampole Files
 */

namespace App\Controller\Result;

class Upload extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array();
		$data['Page'] = array('title' => 'Result :: COA :: Upload');

		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET':
			return $this->_container->view->render($RES, 'page/result/upload.html', $data);
		case 'POST':

			// var_dump($_POST);
			// var_dump($_FILES);

			if (1 == count($_FILES)) {
				if (0 == $_FILES['file']['error']) {

					$import_queue_path = sprintf('%s/var/import/%s', APP_ROOT, $_SESSION['License']['id']);
					$import_queue_file = sprintf('%s/%s', $import_queue_path, urlencode($_FILES['file']['name']));

					if (!is_dir($import_queue_path)) {
						mkdir($import_queue_path, 0755, true);
					}

					move_uploaded_file($_FILES['file']['tmp_name'], $import_queue_file);

				}
			}

			return $RES->withJSON(array(
				'status' => 'success',
			));

			break;
		}
	}

	/**
	 * Generate a Preview of the File
	 * @param [type] $REQ [description]
	 * @param [type] $RES [description]
	 * @param [type] $ARG [description]
	 * @return [type] [description]
	 */
	function preview($REQ, $RES, $ARG)
	{
		// $import_queue_path = sprintf('%s/var/import/%s', APP_ROOT, $_SESSION['License']['id']);

		$pdf_file = $this->resolveFile($_GET['f']);
		$png_file = preg_replace('/pdf$/i', 'png', $pdf_file);

		if (!is_file($png_file)) {
			// Get First Page as PNG
			// Conversion via Imagick CLI
			$cmd = array('/usr/bin/convert');
			$cmd[] = '-quiet';
			$cmd[] = '-background white';
			$cmd[] = '-fill white';
			$cmd[] = sprintf('-crop %dx%d+%d+%d', $w, $h, $x, $y);
			$cmd[] = escapeshellarg(sprintf('%s[0]', $pdf_file)); // the '[0]' bit tells Imagick to do first page only
			$cmd[] = escapeshellarg($png_file);
			//$cmd[] = 'png:-';
			$cmd[] = '2>/dev/null';
			$cmd = implode(' ', $cmd);
			//var_dump($cmd);
			$buf = shell_exec($cmd);
			// var_dump($buf);
		}

		// Emit PNG
		header('content-type: image/png');

		readfile($png_file);

		exit(0);

	}

	protected function resolveFile($f)
	{
		$import_queue_path = sprintf('%s/var/import/%s', APP_ROOT, $_SESSION['License']['id']);

		$f = basename($f);

		if (!is_file($import_queue_path . '/' . $f)) {
			_exit_text(sprintf('Bad File "%s"', $f), 400);
		}

		$import_queue_file = $import_queue_path . '/' . $f;
		$import_queue_file = realpath($import_queue_file);

		return $import_queue_file;
	}
}
