<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use \App\Model\Category;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '\App\Model\Category';

    protected $categoryService;

    public function __construct()
    {
        $this->categoryService=\App::make('categoryService');
        $this->title=trans('admin.categories management');
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    public function detail($id)
    {
        $category=Category::findOrFail($id);
        return redirect('admin/categories/'.$category->id.'/edit');
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category());
        $maxOrder=$this->categoryService->getChildMaxOrder(0);
        $form->text('name', __('Name'));
        $form->select('parent_id',__('admin.Parent category'))->options(Category::selectOptions());
        $form->number('order',__('admin.Order'))->default($maxOrder);
        $form->select('type', __('Type'))->options([2 => '栏目', 1 => '站点']);
        $form->text('uri', __('Uri'));

        $form->hidden('after-save','')->value(1);
        $form->ignore(['after-save']);
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            $tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();

            // 去掉`查看`按钮
            $tools->disableView();

            // 添加一个按钮, 参数可以是字符串, 或者实现了Renderable或Htmlable接口的对象实例
            $tools->append('<a href="'.admin_base_path('categories/create').'" class="btn btn-sm btn-success"><i class="fa fa-new"></i>&nbsp;&nbsp;新建栏目</a>');
        });
        $form->footer(function ($footer) {

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        $getMaxOrderUrl=admin_base_path('/categories/get_max_order/');
        $script = <<<EOT
                    $(function(){
                        $("select.parent_id").on('change',function(){
                            var val=$(this).val();
                            $.get("$getMaxOrderUrl",{pid : val}, function (data) {
                                    if(data)
                                    {
                                        $("#order").val(data[0]);
                                    }
                                }
                            );
                        });
                    })
                    EOT;

        Admin::script($script);

        return $form;
    }

    public function edit($id, Content $content)
    {
        $category=Category::findOrFail($id);
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->row(function (Row $row) use($category){
                $row->column(6, $this->leftTreeView($category->id));
                $row->column(6, $this->form()->edit($category->id));
            });
    }

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return redirect('admin/categories/1/edit');
    }


    private function leftTreeView($selectId=0)
    {
        $ztreeSetting=<<<setting
		var setting = {
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				onClick: onClick
			}
		};
setting;

        $ztree=new \App\Extended\ZTree(Category::class,[],$ztreeSetting,false);
        $redirectURL=admin_base_path('categories');
        $ztree->setScript(
            <<<script
		function onClick(event, treeId, treeNode, clickFlag) {
			window.location.href='$redirectURL/'+treeNode.id+'/edit';
		}
script
        );
        $ztree->setSelectId($selectId);
        return $ztree->renderTree();
    }


    public function getMaxOrder(Request $request)
    {
        $pid=$request->input('pid');
        if(is_numeric($pid) && $pid>=0)
        {
            $maxOrder=$this->categoryService->getChildMaxOrder($pid);
            return [$maxOrder];
        }
        return [0];
    }
}
