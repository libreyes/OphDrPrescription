<?php

class m130821_115628_prescribers_list extends CDbMigration
{
	public function up()
	{

		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('ophdrprescription_prescribers', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');


		$this->addColumn('et_ophdrprescription_details','prescriber','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophdrprescription_details_prescriber_id_fk','et_ophdrprescription_details','prescriber');
		$this->addForeignKey('et_ophdrprescription_details_prescriber_id_fk','et_ophdrprescription_details','prescriber','ophdrprescription_prescribers','id');


	}

	public function down()
	{
		$this->dropForeignKey('et_ophdrprescription_details_prescriber_id_fk','et_ophdrprescription_details');
		$this->dropIndex('et_ophdrprescription_details_prescriber_id_fk','et_ophdrprescription_details');
		$this->dropColumn('et_ophdrprescription_details','prescriber');
		$this->dropTable('ophdrprescription_prescribers');
	}
}