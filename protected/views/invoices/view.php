<?php
/* @var $this InvoicesController */
/* @var $model Invoices */

$this->breadcrumbs=array(
	'Invoices'=>array('index'),
	$model->InvoiceNo,
);

$this->menu=array(
	array('label'=>'List Invoices', 'url'=>array('index')),
	array('label'=>'Create Invoices', 'url'=>array('create')),
	array('label'=>'Update Invoices', 'url'=>array('update', 'id'=>$model->InvoiceNo)),
	array('label'=>'Delete Invoices', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->InvoiceNo),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Invoices', 'url'=>array('admin')),
);
?>

<h1>View Invoices #<?php echo $model->InvoiceNo; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'InvoiceNo',
		'InvoiceDateTime',
		'UserID',
		'Product',
		'TransactionID',
		'Amount',
		'CustomerID',
	),
)); ?>
