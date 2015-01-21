<?php

Yii::import('zii.widgets.grid.CGridView');

class TweetGridView extends CGridView
{

/**
* Renders a table body row.
* @param integer $row the row number (zero-based).
*/
public function renderTableRow($row)
{
	if($this->rowCssClassExpression!==null)
	{
		$data=$this->dataProvider->data[$row];
		echo '<tr '.$this->getRowId($row).' class="'.$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$data)).'">';
	}
	else if(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
	echo '<tr '.$this->getRowId($row).' class="'.$this->rowCssClass[$row%$n].'">';
	else
	echo '<tr '.$this->getRowId($row).'>';
	foreach($this->columns as $column)
	$column->renderDataCell($row);
	echo "</tr>\n";
}

/**
* Renders a table Row ID.
* @param integer $row the row number (zero-based).
*/
public function getRowId($row)
{
	$rowid="id=".$this->dataProvider->data[$row]->tweetid;
	return $rowid;
}
}