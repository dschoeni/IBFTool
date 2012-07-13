<?php
class TreatmentsHasModule extends Sbftool_Db_Table_Abstract
{
	protected $_name = 'treatments_has_module';
	protected $_referenceMap    = array(

			'modules' => array(
					'columns'           => 'module_id',
					'refTableClass'     => 'Modules',
					'refColumns'        => 'id'
			),
			'treatments' => array(
					'columns'           => 'treatments_id',
					'refTableClass'     => 'Treatments',
					'refColumns'        => 'id'
			)
	);

}