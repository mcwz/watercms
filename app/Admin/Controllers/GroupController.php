<?php

namespace App\Admin\Controllers;

use App\Model\Group;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Tree;
use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends AdminController
{
    protected $groupService;
    protected $title;
    public function __construct()
    {
        $this->groupService=App::make('groupService');
        $this->title=trans('admin.user group');
    }
    /**
     * Title for current resource.
     *
     * @var string
     */


    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {

            $content->header($this->title);
            $content->description(trans('admin.user group list'));

            $content->row(function (Row $row) {

                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('/groups'));

                    $maxOrder=$this->groupService->getChildMaxOrder(0);


                    $select=$form->select('parent_id',__('admin.Parent group'))->options(Group::selectOptions());
                    $form->text('title',__('admin.Group name'));
                    $form->number('order',__('admin.Order'))->default($maxOrder);
                    $form->hidden('_token')->default(csrf_token());

                    $getMaxOrderUrl=admin_base_path('/groups/get_max_order/');
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
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Group());

        $grid->column('id', __('Id'));
        $grid->column('parent_id', __('parent group'));
        $grid->column('order', __('admin.Order'));
        $grid->column('title', __('admin.Title'));
        $grid->column('created_at', __('admin.Created at'));
        $grid->column('updated_at', __('admin.Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Group::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('parent', trans('admin.Parent group'))->as(function ($parent) {
            if($parent)
                return $parent->title;
            else
                return '';
        })->label();
        $show->field('order', __('admin.Order'));
        $show->field('title', __('admin.Group name'));
        $show->field('created_at', __('admin.Created at'));
        $show->field('updated_at', __('admin.Updated at'));
        $show->field('users', trans('admin.Users'))->as(function ($users) {
            if($users)
                return $users->pluck('name');
            else
                return '';
        })->label();
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        return Admin::form(Group::class, function (Form $form) {
            $userModel = config('admin.database.users_model');
            $form->display('id', 'ID');
            $form->text('title',__('admin.Group name'));
            $form->select('parent_id',__('admin.Parent group'))->options(Group::selectOptions());
            $form->number('order',__('admin.Order'));
            $form->multipleSelect('users', trans('admin.Users'))->options($userModel::all()->pluck('name', 'id'));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id,Content $content)
    {
        // return Admin::content(function (Content $content) use ($id) {

            $content->header($this->title);
            $content->body($this->form()->edit($id));
            return $content;
        // });
    }


    protected function treeView()
    {
        return Group::tree(function (Tree $tree) {
            $tree->setView([
                'tree'   => 'admin.tree',
                'branch' => 'admin.tree.branch',
            ]);
            $tree->branch(function ($branch) {
                return "{$branch['title']} ";
            });
            $tree->disableCreate();
            return $tree;
        });
    }


    public function getMaxOrder(Request $request)
    {
        $pid=$request->input('pid');
        if(is_numeric($pid) && $pid>=0)
        {
            $maxOrder=$this->groupService->getChildMaxOrder($pid);
            return [$maxOrder];
        }
        return [0];
    }
}
