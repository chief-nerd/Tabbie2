<?php
/**
 * AwsS3.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace common\components;

use Aws\S3\S3Client;
use yii\base\Component;
use yii\base\Exception;

class AmazonS3 extends Component {

	public $dev = false;
	public $key = false;
	public $secret = false;
	public $bucket = "";
	public $region = "eu-central-1";

	private $_client;

	public function __construct($conf = []) {
		parent::__construct($conf);

		if (!$this->dev) {
			if (!$this->key || !$this->secret) throw new Exception("Need key and secret");

			$this->_client = new S3Client(array(
				'credentials' => array(
					'key' => $this->key,
					'secret' => $this->secret,
				),
				'region' => $this->region,
				'version' => 'latest'
			));
		}
	}

	public function save($file, $path) {

		if (!$this->dev) {
			$result = $this->_client->putObject(array(
				'Bucket' => $this->bucket,
				'Key' => $path,
				'SourceFile' => $file->tempName,
				'ACL' => "public-read",
			));

			return $result['ObjectURL'];
		}
		else {
			return $file->saveAs(\Yii::getAlias("@frontend/web/uploads/") . $path) ? $path : null;
		}
	}
}