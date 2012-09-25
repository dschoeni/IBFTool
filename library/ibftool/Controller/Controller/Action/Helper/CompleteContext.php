<?php
class Viser_Controller_Action_Helper_CompleteContext extends Zend_Controller_Action_Helper_ContextSwitch {
	private $added = false;

	public function init() {
		$this->setAutoJsonSerialization(false);

		if (!$this->added) {
			$this->addContext('jpg', array(
				'suffix'	 => 'jpg',
				'headers'	 => array(
					'Content-Type'	 => 'image/jpeg',
				),
			));

			$this->addContext('png', array(
				'suffix'	 => 'png',
				'headers'	 => array('Content-Type' => 'image/png'),
			));

			$this->addContext('js', array(
				'suffix'	 => 'js',
				'headers'	 => array('Content-Type' => 'text/javascript'),
			));


			$this->addContext('css', array(
				'suffix'	 => 'css',
				'headers'	 => array('Content-Type' => 'text/css'),
			));

			$this->addContext('html', array(
				'suffix' => 'ajax',
			));

			$this->addContext('csv', array(
				'suffix'	 => 'csv',
				'headers'	 => array('Content-Type' => 'text/csv'),
			));

			$this->addContext('vcard', array(
				'suffix'	 => 'vcard',
				'headers'	 => array(
					'Content-Type' => 'text/vcard',
					'Content-Disposition'	 => 'attachment; filename=customer.vcf',
				),
			));

			$this->addContext('ics', array(
				'suffix'	 => 'ics',
				'headers'	 => array(
					'Content-Type'	 => 'text/calendar',
					'Content-Disposition: inline; filename=calendar.ics',
				),
			));

			$this->addContext('pdf', array(
				'suffix'	 => 'pdf',
				'headers'	 => array(
					'Content-Type'			 => 'application/pdf',
					'Content-Disposition'	 => 'attachment; filename=clubzone.pdf',
				),
			));

			$this->addContext('xlsx', array('suffix'	 => 'xlsx', 'headers'	 => array(
				'Content-Type'				 => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'Pragma'					 => 'public',
				'Expires'					 => '0',
				'Cache-Control'				 => 'must-revalidate, post-check=0, pre-check=0',
				'Content-Disposition'		 => 'attachment;filename=export.xlsx',
				'Content-Transfer-Encoding'	 => 'binary')
			));

			$this->addContext('doc', array('suffix'	 => 'doc', 'headers'	 => array(
				'Content-Type'				 => 'application/msword',
				'Pragma'					 => 'public',
				'Expires'					 => '0',
				'Cache-Control'				 => 'must-revalidate, post-check=0, pre-check=0',
				'Content-Disposition'		 => 'attachment;filename=export.doc',
				'Content-Transfer-Encoding'	 => 'binary')
			));

			$this->addContext('docx', array('suffix'	 => 'docx', 'headers'	 => array(
				'Content-Type'				 => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'Pragma'					 => 'public',
				'Expires'					 => '0',
				'Cache-Control'				 => 'must-revalidate, post-check=0, pre-check=0',
				'Content-Disposition'		 => 'attachment;filename=export.docx',
				'Content-Transfer-Encoding'	 => 'binary')
			));

			$this->added = true;
		}

		parent::init();
	}
}