<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * The followings are the available columns in table 'ophdrprescription_item':
 * @property string $id
 * @property string $dose
 * @property DrugDuration $duration
 * @property DrugFrequency $frequency
 * @property DrugRoute $route
 * @property DrugRouteOption $route_option
 * @property Drug $drug
 * @property Prescription $prescription
 * @property OphDrPrescription_ItemTaper[] $tapers
 */
class OphDrPrescription_Item extends BaseActiveRecord {

	/**
	 * Returns the static model of the specified AR class.
	 * @return OphDrPrescription_Item the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'ophdrprescription_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('prescription_id, drug_id, dose, route_id, frequency_id, duration_id', 'required'),
				array('route_option_id', 'validateRouteOption'),
				array('route_option_id', 'safe'),
				//array('', 'required'),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id, dose, prescription_id, drug_id, route_id, route_option_id, frequency_id, duration_id', 'safe', 'on' => 'search'),
		);
	}
	
	public function validateRouteOption($attribute, $params) {
		if($this->route && $this->route->options) {
			foreach($this->route->options as $option) {
				if($option->id == $this->route_option_id) {
					// Option is valid for this route
					return;
				}
			}
		} else {
			// Route options are ignored
			return;
		}
		$this->addError($attribute, 'Route requires option selection');
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'tapers' => array(self::HAS_MANY, 'OphDrPrescription_ItemTaper', 'item_id'),
				'prescription' => array(self::BELONGS_TO, 'Element_OphDrPrescription_Details', 'prescription_id'),
				'duration' => array(self::BELONGS_TO, 'DrugDuration', 'duration_id'),
				'frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'frequency_id'),
				'route' => array(self::BELONGS_TO, 'DrugRoute', 'route_id'),
				'route_option' => array(self::BELONGS_TO, 'DrugRouteOption', 'route_option_id'),
				'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
				'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
		);
	}

	public function getDrug() {
		return Drug::model()->discontinued()->findByPk($this->drug_id);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'drug_id' => 'Drug',
				'dose' => 'Dose',
				'duration_id' => 'Duration',
				'frequency_id' => 'Frequency',
				'route_id' => 'Route',
				'route_option_id' => 'Options'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('dose', $this->dose, true);
		$criteria->compare('prescription_id', $this->prescription_id, true);
		$criteria->compare('drug_id', $this->drug_id, true);
		$criteria->compare('duration_id', $this->duration_id, true);
		$criteria->compare('frequency_id', $this->frequency_id, true);
		$criteria->compare('route_id', $this->route_id, true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
		));
	}

	public function getDescription() {
		$return = $this->drug->name;
		$return .= ', ' . $this->dose;
		$return .= ' ' . $this->frequency->name;
		$return .= ' ' . $this->route->name;
		if($this->route_option) {
			$return .= ' (' . $this->route_option->name . ')';
		}
		$return .= ' for ' . $this->duration->name;
		return $return;
	}

	public function loadDefaults() {
		if($this->drug) {
			$this->duration_id = $this->drug->default_duration_id;
			$this->frequency_id = $this->drug->default_frequency_id;
			$this->route_id = $this->drug->default_route_id;
			$this->dose = trim($this->drug->default_dose . ' ' . $this->drug->dose_unit);
		}
	}

	public function availableDurations() {
		return DrugDuration::model()->findAll(array('order' => 'display_order'));
	}

	public function availableFrequencies() {
		return DrugFrequency::model()->findAll(array('order' => 'name'));
	}

	public function availableRoutes() {
		return DrugRoute::model()->findAll(array('order' => 'name'));
	}

}
