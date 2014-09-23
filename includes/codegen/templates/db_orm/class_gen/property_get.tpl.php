		/**
		 * Override method to perform a property "Get"
		 * This will get the value of $strName
		 *
		 * @param string $strName Name of the property to get
		 * @return mixed
		 */
		public function __get($strName) {
			switch ($strName) {
				///////////////////
				// Member Variables
				///////////////////
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
				case '<?php echo $objColumn->PropertyName  ?>':
					/**
					 * Gets the value for <?php echo $objColumn->VariableName  ?> <?php if ($objColumn->Identity) print '(Read-Only PK)'; else if ($objColumn->PrimaryKey) print '(PK)'; else if ($objColumn->Timestamp) print '(Read-Only Timestamp)'; else if ($objColumn->Unique) print '(Unique)'; else if ($objColumn->NotNull) print '(Not Null)'; ?>

					 * @return <?php echo $objColumn->VariableType  ?>

					 */
					return $this-><?php echo $objColumn->VariableName  ?>;

<?php } ?>

				///////////////////
				// Member Objects
				///////////////////
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php if (($objColumn->Reference) && (!$objColumn->Reference->IsType)) { ?>
				case '<?php echo $objColumn->Reference->PropertyName  ?>':
					/**
					 * Gets the value for the <?php echo $objColumn->Reference->VariableType  ?> object referenced by <?php echo $objColumn->VariableName  ?> <?php if ($objColumn->Identity) print '(Read-Only PK)'; else if ($objColumn->PrimaryKey) print '(PK)'; else if ($objColumn->Unique) print '(Unique)'; else if ($objColumn->NotNull) print '(Not Null)'; ?>

					 * @return <?php echo $objColumn->Reference->VariableType  ?>

					 */
					try {
						if ((!$this-><?php echo $objColumn->Reference->VariableName  ?>) && (!is_null($this-><?php echo $objColumn->VariableName  ?>)))
							$this-><?php echo $objColumn->Reference->VariableName  ?> = <?php echo $objColumn->Reference->VariableType  ?>::Load($this-><?php echo $objColumn->VariableName  ?>);
						return $this-><?php echo $objColumn->Reference->VariableName  ?>;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

<?php } ?>
<?php } ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?>
<?php if ($objReverseReference->Unique) { ?>
<?php $objReverseReferenceTable = $objCodeGen->TableArray[strtolower($objReverseReference->Table)]; ?>
<?php $objReverseReferenceColumn = $objReverseReferenceTable->ColumnArray[strtolower($objReverseReference->Column)]; ?>
				case '<?php echo $objReverseReference->ObjectPropertyName  ?>':
					/**
					 * Gets the value for the <?php echo $objReverseReference->VariableType  ?> object that uniquely references this <?php echo $objTable->ClassName  ?>

					 * by <?php echo $objReverseReference->ObjectMemberVariable  ?> (Unique)
					 * @return <?php echo $objReverseReference->VariableType  ?>

					 */
					try {
						if (!$this->__blnRestored ||
								$this-><?php echo $objReverseReference->ObjectMemberVariable  ?> === false)
							// Either this is a new object, or we've attempted early binding -- and the reverse reference object does not exist
							return null;
						if (!$this-><?php echo $objReverseReference->ObjectMemberVariable  ?>)
							$this-><?php echo $objReverseReference->ObjectMemberVariable  ?> = <?php echo $objReverseReference->VariableType  ?>::LoadBy<?php echo $objReverseReferenceColumn->PropertyName  ?>(<?php echo $objCodeGen->ImplodeObjectArray(', ', '$this->', '', 'VariableName', $objTable->PrimaryKeyColumnArray)  ?>);
						return $this-><?php echo $objReverseReference->ObjectMemberVariable  ?>;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

<?php } ?>
<?php } ?>

				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

<?php foreach ($objTable->ManyToManyReferenceArray as $objReference) { ?>
<?php 
	$objAssociatedTable = $objCodeGen->GetTable($objReference->AssociatedTable);
	$varPrefix = (is_a($objAssociatedTable, 'QTypeTable') ? '_int' : '_obj');
	$varType = (is_a($objAssociatedTable, 'QTypeTable') ? 'integer' : $objReference->VariableType);
?>
				case '<?php echo $objReference->ObjectDescription ?>':
				case '_<?php echo $objReference->ObjectDescription ?>': // for backwards compatibility
					/**
					 * Gets the value for the private <?php echo $varPrefix . $objReference->ObjectDescription ?> (Read-Only)
					 * if set due to an expansion on the <?php echo $objReference->Table ?> association table
					 * @return <?php echo $varType  ?>

					 */
					return $this-><?php echo $varPrefix . $objReference->ObjectDescription ?>;

				case '<?php echo $objReference->ObjectDescription ?>Array':
				case '_<?php echo $objReference->ObjectDescription ?>Array': // for backwards compatibility
					/**
					 * Gets the value for the private <?php echo $varPrefix . $objReference->ObjectDescription ?>Array (Read-Only)
					 * if set due to an ExpandAsArray on the <?php echo $objReference->Table ?> association table
					 * @return <?php echo $varType ?>[]
					 */
					return $this-><?php echo $varPrefix . $objReference->ObjectDescription ?>Array;


<?php } ?><?php foreach ($objTable->ReverseReferenceArray as $objReference) { ?><?php if (!$objReference->Unique) { ?>
				case '<?php echo $objReference->ObjectDescription ?>':
				case '_<?php echo $objReference->ObjectDescription ?>':
					/**
					 * Gets the value for the private _obj<?php echo $objReference->ObjectDescription ?> (Read-Only)
					 * if set due to an expansion on the <?php echo $objReference->Table ?>.<?php echo $objReference->Column ?> reverse relationship
					 * @return <?php echo $objReference->VariableType  ?>

					 */
					return $this->_obj<?php echo $objReference->ObjectDescription ?>;

				case '<?php echo $objReference->ObjectDescription ?>Array':
				case '_<?php echo $objReference->ObjectDescription ?>Array':
					/**
					 * Gets the value for the private _obj<?php echo $objReference->ObjectDescription ?>Array (Read-Only)
					 * if set due to an ExpandAsArray on the <?php echo $objReference->Table ?>.<?php echo $objReference->Column ?> reverse relationship
					 * @return <?php echo $objReference->VariableType  ?>[]
					 */
					return $this->_obj<?php echo $objReference->ObjectDescription ?>Array;

<?php } ?><?php } ?>

				case '__Restored':
					return $this->__blnRestored;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}