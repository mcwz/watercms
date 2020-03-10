<?php

namespace App\Admin\Controllers;

use App;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use \App\Model\Category;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use App\Extended\Tree;

class CategoryController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;
    protected $categoryService;

    public function __construct()
    {
        $this->categoryService=App::make('categoryService');
        $this->title=trans('admin.categories management');
    }

    private function leftTreeView()
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

        $ztree=new App\Extended\ZTree(Category::class,[],$ztreeSetting,false);
        $ztree->setScript(
            <<<script
		function onClick(event, treeId, treeNode, clickFlag) {
			console.log(treeNode);
		}
script
        );
        return $ztree->renderTree();
    }

    private function buttonArea(){

    }

    private function categoryDetail(Category $category)
    {

        $show = new Show($category);

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('parent_id', __('Parent id'));
        $show->field('order', __('Order'));
        $show->field('type', __('Type'));
        $show->field('uri', __('Uri'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    public function category(Category $category)
    {
        if(!$category)return redirect('admin/categories/1');

        return Admin::content(function (Content $content) use($category){

            $content->header($this->title);
            $content->description(trans('admin.Categories list'));

            $content->row(function (Row $row) use($category){

                $row->column(6, $this->leftTreeView());

                $row->column(6, $this->categoryDetail($category));
            });



        });
    }

    private function newForm()
    {
        $form = new \Encore\Admin\Widgets\Form();
        $form->action(admin_base_path('/categories'));

        $maxOrder=$this->categoryService->getChildMaxOrder(0);


        $select=$form->select('parent_id',__('admin.Parent category'))->options(Category::selectOptions());
        $form->text('name',__('admin.Category name'));
        $form->number('order',__('admin.Order'))->default($maxOrder);
        $form->hidden('_token')->default(csrf_token());

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
        //$column->append((new Box(trans('admin.new'), $form))->style('success'));
    }


    public function index1(Content $content)
    {
        return Admin::content(function (Content $content) {

            $content->header($this->title);
            $content->description(trans('admin.Categories list'));

            $content->row(function (Row $row) {

                $row->column(6, $this->leftTreeView());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('/categories'));

                    $maxOrder=$this->categoryService->getChildMaxOrder(0);


                    $select=$form->select('parent_id',__('admin.Parent category'))->options(Category::selectOptions());
                    $form->text('name',__('admin.Category name'));
                    $form->number('order',__('admin.Order'))->default($maxOrder);
                    $form->hidden('_token')->default(csrf_token());

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
                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });



        });
    }


    protected function treeView()
    {

    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('parent_id', __('Parent id'));
        $grid->column('order', __('Order'));
        $grid->column('type', __('Type'));
        $grid->column('uri', __('Uri'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }



    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category());

        $form->text('name', __('Name'));
        $form->number('parent_id', __('Parent id'));
        $form->number('order', __('Order'));
        $form->switch('type', __('Type'))->default(2);
        $form->text('uri', __('Uri'));

        return $form;
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
