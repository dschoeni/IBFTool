<?php
class TreatmentsHasUsers extends Sbftool_Db_Table_Abstract
{
	protected $_name = 'treatments_has_users';

	protected $_referenceMap    = array(

			'users' => array(
					'columns'           => 'users_id',
					'refTableClass'     => 'Users',
					'refColumns'        => 'id'
			),
			'treatments' => array(
					'columns'           => 'treatments_id',
					'refTableClass'     => 'Treatments',
					'refColumns'        => 'id'
			)
	);


}