<?php

class DeedMasterController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('admin','printfrom','getdeed','getdeedfromcontract','create','update','LandInfo','SaveDeed','Print','canceldeed','landsfind','updateinfo'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionupdateinfo()
	{
		if(isset($_POST["data"]))
		{
			$data = json_decode($_POST["data"]);
			
			$d = DeedMaster::model()->findByPk($data->DeedID);
			
			if($data->type=="PO")
			{
				$d->PreviousOwners = $data->PreviousOwners;
				$d->save();
			}
			else
			{
				$d->Remarks = $data->Remarks;
				$d->save();
			}
			
			print CJSON::encode('done');
		}
	}
	
	public function actioncanceldeed()
	{
		if(isset($_POST["data"])) //check that this action is only called using POST.. not get, not regular.
		{
			$deedid	= json_decode($_POST['data']);
			$deedtocancel = DeedMaster::model()->findByPk($deedid);
			$deedtocancel->Remarks = "cancelled";
			$deedtocancel->save();
		}
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionCreate()
	{
		//$deedModel = new DeedMaster;
		/*$LandIDs = array();
		
		$sc = new CDbCriteria; //$sc->limit = 1300;
		$lands = LandMaster::model()->findAll($sc);
		
		foreach($lands as $land)
		{
			$LandIDs[] = $land->LandID;
		}
		
		$customerList =CustomerMaster::model()->FindAll($sc);
		$customerArray = array();

		foreach($customerList as $customer)
		{   
			$customerArray[] = $customer->CustomerNameArabic;
		}
*/
		$this->render('create', array(
			//'LandIDs'=>$LandIDs,
			//'customerNames'=>$customerArray,
			));
			
		
	}

	public function actionLandInfo()
	{
		$result = new StdClass;
		$result->activedeeds =array();
		$result->deedDetails = array();
		$result->share = array();
		$result->lands = array();
		$result->errors = array();
		$id = array(); //temporary usage later
		if(isset($_POST["data"])) //check that this action is only called using POST.. not get, not regular.
		{
			$LandID = json_decode($_POST['data']);
			
			/*(check if it exists in land database)
				if NO, then message asking create NEW? On clicking YES, take to LandMaster create-new
				If YES, then...
					(check if there is an ACTIVE deed associated with it)
						if NO,
							Display Land Info back in the view and let the view continue
						if YES,
							Alert that there is an active deed and Display tabulated info of the deed(s) n owners.
			*/
			
			$landinfo = LandMaster::model()->findAllByPk($LandID); //could return multiples
			
			if ($landinfo) //if i found it... 
			{
				foreach($landinfo as $land)
				{
					$id[] = $land->LandID; //pull all their id's ..if multiples
				}
				
				$sc = new CDbCriteria; 
				$sc->condition = 'LandID = :id AND Remarks!=:cancelled';
				$sc->params = array(':id'=>$id[0], ':cancelled'=>'cancelled');
				//$sc->limit = 2000;
				
				$deeds = DeedMaster::model()->findAll($sc);
				if ($deeds)
				{
					$activedeeds[] = array();
					foreach($deeds as $deed)
					{
						$result->activedeeds[] = $deed;
						$details = $deed->deedDetails;
						foreach($details as $deedline)
						{
							$result->deedDetails[] =$deedline->Share."---------------".$deedline->customer->CustomerNameArabic;
							//$result->share[] = $deedline->Share;
						}
					}
					$result->lands = $landinfo;
						
					print CJSON::encode($result); //show the deeds that are there with this land! you cant create a new deed till you cancel the old deeds or create a new land id! 
				}
				else
				{
					$result->lands = $landinfo;
					print CJSON::encode($result); //send back the land object..no deed associated with it. safe to continue creating a new deed
				}
			}
			else
			{	
				$result->errors = "Land ID not in database";
				print CJSON::encode($result);
			}	
		
		}
			
	}
	
	public function actionSaveDeed()
	{
		if(isset($_POST["data"])) //check that this action is only called using POST.. not get, not regular.
		{
			$data = json_decode($_POST['data']);
										/*buyers: buyers,
										landid: landid,
										LocationID: LocationID,
										location: location,
										Plot_No: Plot_No,
										Land_Type: Land_Type,
										len: len,
										width: width,
										TotalArea: TotalArea,
										AreaUnit: AreaUnit,
										North: North,
										South: South,
										East: East,
										West: West,
										Remarks: Remarks,
										CreatedDate: CreatedDate,
										HijriDate: HijriDate,
										deedRemarks: deedRemarks,*/
			//NOTE NOTE NOTE: Check that there is no existing Deed with the Land ID you're about to save with ! 
			$deed = new DeedMaster;
			$deed->LandID = $data->landid;
			$deed->UserID = Yii::app()->User->ID;
			$deed->DateCreated = $data->CreatedDate;
			$deed->DateHijri = $data->HijriDate;
			$deed->Remarks = $data->deedRemarks;
						
			if($deed->save())
				{
					foreach ($data->buyers as $buyer)
					{
						$deedline = new DeedDetails;
						$deedline->DeedID = $deed->DeedID;
						$deedline->CustomerID = $buyer->buyerid;
						
						if($data->equals=="yes")
						{
							$deedline->Share = "كامل الحصص";
							$deedline->save();
						}
						else
						{
							$deedline->Share = $buyer->shareval;
							$deedline->save();
						}
					}
				
					$landupdate = LandMaster::model()->findByPk($data->landid);
					if ($landupdate)
					{						
						$landupdate->LandID = $data->landid;
						$landupdate->LocationID = $data->LocationID;
						$landupdate->Plot_No = $data->Plot_No;
						$landupdate->Piece = $data->Piece;
						$landupdate->location = $data->location;
						$landupdate->Land_Type = $data->Land_Type;
						$landupdate->TotalArea = floatval($data->TotalArea);
						$landupdate->length = floatval($data->len);
						$landupdate->width = floatval($data->width);
						$landupdate->AreaUnit = $data->AreaUnit;
						$landupdate->Remarks = $data->Remarks;
						$landupdate->North = $data->North;
						$landupdate->South = $data->South;
						$landupdate->East = $data->East;
						$landupdate->West = $data->West;
						$landupdate->save();
						
						print CJSON::encode("success".$deed->DeedID);
						
					}
					else
					{
						
						$landnew = new LandMaster;
						$landnew->LandID = $data->landid;
						$landnew->LocationID = $data->LocationID;
						$landnew->Plot_No = $data->Plot_No;
						$landnew->Piece = $data->Piece;
						$landnew->location = $data->location;
						$landnew->Land_Type = $data->Land_Type;
						$landnew->TotalArea = $data->TotalArea;
						$landnew->length = $data->len;
						$landnew->width = $data->width;
						$landnew->AreaUnit = $data->AreaUnit;
						$landnew->Remarks = $data->Remarks;
						$landnew->North = $data->North;
						$landnew->South = $data->South;
						$landnew->East = $data->East;
						$landnew->West = $data->West;
						$landnew->save();
						print CJSON::encode("success".$deed->DeedID);
					}
					
				
				}
			else
				print CJSON::encode("error");
			
		}
	}
	
	public function actionPrintFrom()
	{
		$this->render('printfrom');
	}

	public function actiongetdeed()
	{
		
		if(isset($_POST["data"]))
		{
			$qtxt = "SELECT DeedID from DeedMaster WHERE LandID LIKE :landid AND Remarks NOT LIKE 'cancelled'";
			$command = Yii::app()->db->createCommand($qtxt);
			$command->bindValue(':landid',json_decode($_POST["data"]),PDO::PARAM_STR);
			$res = $command->queryColumn();
			
		}
		
		print CJSON::encode($res);
	}
	
	public function actiongetdeedfromcontract()
	{
		if(isset($_POST["data"]))
		{
			$qtxt = "SELECT LandID from ContractsMaster WHERE ContractsID LIKE :contractid ";
			$command = Yii::app()->db->createCommand($qtxt);
			$command->bindValue(':contractid',json_decode($_POST["data"]),PDO::PARAM_STR);
			$lid = $command->queryColumn();
			
			$qtxt = "SELECT DeedID from DeedMaster WHERE LandID LIKE :landid AND Remarks NOT LIKE 'cancelled'";
			$command = Yii::app()->db->createCommand($qtxt);
			$command->bindValue(':landid',$lid[0],PDO::PARAM_STR);
			$res = $command->queryColumn();
						
		}
		
		print CJSON::encode($res);
	
	}
	
	
	public function actionlandsfind()
	{
		
		if (isset($_GET['term'])) {
			$qtxt = 'SELECT LandID from LandMaster WHERE LandID LIKE :name';
			$command = Yii::app()->db->createCommand($qtxt);
			$command->bindValue(':name','%'.$_GET['term'].'%',PDO::PARAM_STR);
			$res = $command->queryColumn();
		}
		print CJSON::encode($res);
	
	}
	
	
	public function actionPrint($id)
	{
		$deed = DeedMaster::model()->findByPk($id);
		$d = $deed->DeedID;
		$cnt = DeedDetails::model()->count('DeedID LIKE :id',array(':id'=>$d));
		
		$deedtracker = new DeedTracker;
		$deedtracker->DeedID = $d;
		$deedtracker->LandID = $deed->LandID;
		$deedtracker->UserID = Yii::app()->User->ID;
		$deedtracker->DateTime = date('d-m-Y, G:i A');
		$deedtracker->Status = "طباعة";
		$deedtracker->save();
		
		$this->renderpartial('printout',array('deed'=>$deed,'cnt'=>$cnt,));
	}
	
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 "*" DONT FORGET TO REMOVE THE QUOTES ON THE STAR/
	public function actionCreate()
	{
		$model=new DeedMaster;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DeedMaster']))
		{
			$model->attributes=$_POST['DeedMaster'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->DeedID));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	----------------------------------------------------------------------------------------------------
	*/
	
	
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DeedMaster']))
		{
			$model->attributes=$_POST['DeedMaster'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->DeedID));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('DeedMaster');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DeedMaster('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DeedMaster']))
			$model->attributes=$_GET['DeedMaster'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=DeedMaster::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='deed-master-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}