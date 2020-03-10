<?php


namespace App\Extended;


use App\Model\Category;
use Encore\Admin\Facades\Admin;

class ZTree
{

    private $model;
    private $setting=[
        'id'=>'id',
        'pId'=>'parent_id',
        'name'=>'name',
        'openId'=>0,
    ];
    private $nodeTreeSet;
    private $script;
    private $simpleData;

    public function __construct($model,$setting=[],$nodeTreeSet="var setting ={}",$simpleData=true)
    {
        $this->model=$model;
        $this->setting=array_merge($this->setting,$setting);
        $this->nodeTreeSet=$nodeTreeSet;
        $this->simpleData=$simpleData;
    }

    public function setNodeTreeSet($nodeTreeSet)
    {
        $this->nodeTreeSet=$nodeTreeSet;
    }

    public function setScript($script)
    {
        $this->script=$script;
    }

    public function renderTree()
    {
        $uuid=uniqid();
        Admin::js('/assets/jquery.ztree.core.min.js');
        Admin::css('/assets/zTreeStyle/zTreeStyle.css');
        $nodeData=json_encode($this->treeNodeDataTransfer());
        $simpleDataSetting=($this->simpleData?'if(setting.data){setting.data.simpleData={enable:true}}else{setting.data={simpleData:{enable:true}}}':'');
        Admin::script(<<<SCRIPT

        $this->nodeTreeSet
        $simpleDataSetting
        $this->script
		var zNodes =$nodeData;

		$(document).ready(function(){
			$.fn.zTree.init($("#$uuid"), setting, zNodes);
		});
SCRIPT

        );
        return "<div><ul id=\"$uuid\" class=\"ztree\"></ul></div>";
    }



    private function treeNodeDataTransfer()
    {
        $nodeData=$this->model::all();
        $treeData=[];
        foreach ($nodeData as $node) {
            $lineData['id']=$node[$this->setting['id']];
            $lineData['pId']=$node[$this->setting['pId']];
            $lineData['name']=$node[$this->setting['name']];
            if($this->setting['openId']==$node[$this->setting['id']])
            {
                $lineData['open']=true;
            }
            $treeData[]=$lineData;
            $lineData=[];
        }
        return $treeData;
    }
}
