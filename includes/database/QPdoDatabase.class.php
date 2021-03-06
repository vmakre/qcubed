<?php
/**
 * PDO Generic database driver
 * @abstract
 * @author Marcos Sanchez [marcosdsanchez at thinkclear dot com dot ar]
 * @package DatabaseAdapters
 */
abstract class QPdoDatabase extends QDatabaseBase {
		const Adapter = 'Generic PDO Adapter (Abstract)';

		/**
		 * @var PDO connection handler
		 * @access protected
		 */
		protected $objPdo;
		/**
		 * @var PDOStatement most recent query result
		 * @access protected
		 */
		protected $objMostRecentResult;


		protected function ExecuteNonQuery($strNonQuery) {
				// Perform the Query
				$objResult = $this->objPdo->query($strNonQuery);
				if ($objResult === false)
						throw new QPdoDatabaseException($this->objPdo->errorInfo(), $this->objPdo->errorCode(), $strNonQuery);
				$this->objMostRecentResult = $objResult;
		}

		public function __get($strName) {
				switch ($strName) {
						case 'AffectedRows':
								return $this->objMostRecentResult->rowCount();
						default:
								try {
										return parent::__get($strName);
								} catch (QCallerException $objExc) {
										$objExc->IncrementOffset();
										throw $objExc;
								}
				}
		}

		public function Close() {
				$this->objPdo = null;
		}

		
		protected function ExecuteTransactionBegin() {
				if (!$this->blnConnectedFlag) { 
					$this->Connect(); 
				}
				$this->objPdo->beginTransaction();
		}

		protected function ExecuteTransactionCommit() {
				$this->objPdo->commit();
		}

		protected function ExecuteTransactionRollBack() {
				$this->objPdo->rollback();
		}

}
/**
 * Class QPdoDatabaseResult: Class to handle results sent by database upon querying
 *
 * @abstract
 */
abstract class QPdoDatabaseResult extends QDatabaseResultBase {
		/**
		 * @var PDOStatement Query result
		 * @access protected
		 */
		protected $objPdoResult;
		/**
		 * @var PDO Connection object
		 * @access protected
		 */
		protected $objPdo;

		public function __construct($objResult, QPdoDatabase $objDb) {
				$this->objPdoResult = $objResult;
				$this->objPdo = $objDb;
		}

		public function FetchArray() {
				return $this->objPdoResult->fetch();
		}

		public function FetchRow() {
				return $this->objPdoResult->fetch(PDO::FETCH_NUM);
		}

		public function CountRows() {
				return $this->objPdoResult->rowCount();
		}

		public function CountFields() {
				return $this->objPdoResult->columnCount();
		}

		public function Close() {
				$this->objPdoResult = null;
		}

		public function GetRows() {
				$objDbRowArray = array();
				while ($objDbRow = $this->GetNextRow())
						array_push($objDbRowArray, $objDbRow);
				return $objDbRowArray;
		}
}
/**
 * PdoDatabaseException
 * 
 * @abstract
 */
class QPdoDatabaseException extends QDatabaseExceptionBase {
		public function __construct($strMessage, $intNumber, $strQuery) {
				parent::__construct(sprintf("PDO %s", $strMessage[2]), 2);
				$this->intErrorNumber = $intNumber;
				$this->strQuery = $strQuery;
		}
}