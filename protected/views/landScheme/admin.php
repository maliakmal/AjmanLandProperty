<?php
/* @var $this LandSchemeController */
/* @var $model LandScheme */

$this->breadcrumbs=array(
	'Land Schemes'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List LandScheme', 'url'=>array('index')),
	array('label'=>'Create LandScheme', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('land-scheme-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Land Schemes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'land-scheme-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'SchemeID',
		'LandID',
		'SchemeDrawing',
		'MunicipalityID',
		'DateInserted',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
